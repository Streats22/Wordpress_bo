'use strict';

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

/*!
* Copyright (C) Oh!Fuchs, if not stated otherwise
* Written by Patrick Paul, Oh!Fuchs, Apparelcuts <patrick@ohfuchs.com> 2015-2019
*/

//@prepros-prepend _copyright.js
//@prepros-append synchronization/tasks-proto.js
//@prepros-append synchronization/tasks-articles.js
//@prepros-append synchronization/tasks-producttypes.js
//@prepros-append synchronization/tasks.js

;(function () {
  function Plugin(sfw, $) {

    var sync = sfw.sync = {};

    var screen = sync.screen = {};

    /**
     * prepare the sync screen
     *
     * @return {[type]} [description]
     */

    screen.init = function () {
      var _this = this;

      this.$startbutton = $('#sfw-sync-start.button');
      this.$stopbutton = $('#sfw-sync-stop.button');

      this.$startbutton.one('click', function () {

        // disable start button
        _this.$startbutton.addClass('disabled');

        // show metaboxes
        $('#mb-sync-stats,#mb-sync-log').removeClass('sfw-hidden');

        // start sync,
        sync.run()
        // disable stop button
        .always(function () {
          return _this.$stopbutton.addClass('disabled');
        });

        // allow abort
        _this.$stopbutton.removeClass('disabled').on('click', function () {
          sync.forceStop = true;
          _this.$stopbutton.addClass('disabled');
        });
      }).removeClass('disabled');

      wp.hooks.doAction('sfw.sync.init', sfw, $);
    };

    wp.hooks.addAction('sfw.ready', 'sfw.sync.screen.init', function () {
      return screen.init();
    });

    /**
     * Stage
     *
     * the Stage show visual Feedback in Form of Images and Icons
     *
     * @type {[type]}
     */

    var stage = screen.stage = {};

    stage.init = function () {
      stage.$el = $('#sfw-stage');
      stage.default = stage.$el.children();
    };

    wp.hooks.addAction('sfw.ready', 'sfw.sync.stage.init', stage.init);

    stage.show = function (mixed) {
      this.clear();
      this.$el.append(mixed);
    };

    stage.clear = function () {
      this.$el.children().detach();
    };

    stage.reset = function () {
      this.show(this.default);
    };

    /**
     * ProgressBar Proto
     *
     * @param  {[type]} $el [description]
     * @return {[type]}     [description]
     */

    var ProgressBar = function ProgressBar($el) {
      var _this2 = this;

      this.$el = $el;
      this.percentage = 0;

      this.set = function (percentage, label) {
        _this2.setProgress(percentage);
        label !== undefined && _this2.setLabel(label);
        return _this2;
      };

      this.setProgress = function (percentage) {
        percentage = Math.min(100, Math.max(0, percentage));
        percentage = Math.ceil(percentage * 100) / 100;
        _this2.percentage = percentage;
        // console.log( this.$el.attr('id'), percentage );
        _this2.refreshUI();
        return _this2;
      };

      this.setLabel = function (label) {
        _this2.label = label;
        _this2.refreshUI();
        return _this2;
      };

      this.reset = function () {
        _this2.resetProgress();
        _this2.resetLabel();
        return _this2;
      };

      this.resetProgress = function () {
        return _this2.setProgress(0);
      };
      this.resetLabel = function () {
        return _this2.setLabel('');
      };

      this.addProgress = function (percentage) {
        return _this2.set(_this2.percentage + percentage);
      };
      this.add = function (percentage) {
        return _this2.addProgress(percentage);
      };

      this.refreshUI = function () {
        this.$el.find('.-sfw-progress').css('width', this.percentage + '%');
        this.$el.find('.-sfw-label').text(this.label);
      };
    };

    /**
     * ProgressBars
     *
     * @param  {[type]} type [description]
     * @return {[type]}      [description]
     */

    var progressbars = screen.progressbars = {};

    progressbars.init = function () {
      progressbars.primary = new ProgressBar($('#sfw-progress-primary'));
      progressbars.secondary = new ProgressBar($('#sfw-progress-secondary'));
    };
    wp.hooks.addAction('sfw.ready', 'sfw.sync.progressbars.init', progressbars.init);

    progressbars.get = function (type) {
      type = type === 'primary' ? type : 'secondary';
      return this[type];
    };

    /**
     * Stats Metabox
     *
     * @param  {[type]} type [description]
     * @return {[type]}      [description]
     */

    var stats = screen.stats = {};

    stats.init = function () {
      this.$box = $('#mb-sync-stats>.inside');
    };
    wp.hooks.addAction('sfw.ready', 'sfw.sync.stats.init', function () {
      return stats.init();
    });

    sync.group = function (name) {

      name = name || sfw.__('Other', 'apparelcuts-spreadshirt');

      var $el = stats.$box.find('[data-stats-group="' + name + '"]');

      if (!$el.length) {
        $el = $('<div data-stats-group="' + name + '" class=""></div>').append('<h4>' + name + '</h4>');
        $el.appendTo(stats.$box);
      }

      return $el;
    };

    sync.stat = stats.update = function (label) {
      var value = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '-';
      var group = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : '';

      var key = group + label;
      var $el = stats.$box.find('[data-stats-label="' + key + '"]');

      if (!$el.length) {
        $el = $('<div class="grid-x" data-stats-label="' + key + '"><div class="cell small-8 --label">' + label + '</div><div class="cell small-4 --value"></div></div>');
        $el.appendTo(this.group(group));
      }

      $el.find('.--value').html(value);
    };

    /**
     * register a new task
     *
     * @param  {String}      handle       [description]
     * @param  {Function}    callback     should return a promise
     * @param  array|false   dependencies array of handles or false
     * @return {[type]}                [description]
     */

    sync.registered_tasks = {};

    sync.registerTask = function (handle, task) {

      task = $.extend({}, {
        handle: handle,
        label: handle,
        callback: null,
        dependencies: [],
        data: false
      }, task);

      this.registered_tasks[handle] = task;
    };

    /**
     * array of queued tasks
     *
     * @type {Array}
     */

    sync.queued_tasks = [];

    /**
     * enqueue a task
     *
     * @param  {[type]} handle [description]
     * @param  {[type]} task   [description]
     * @return {[type]}        [description]
     */

    sync.enqueueTask = function (handle, task) {

      this.queued_tasks.push(handle);
      task && sync.registerTask(handle, task);
    };

    /**
     * retrieve a Task
     *
     * @param  {[type]} handle [description]
     * @return {[type]}        [description]
     */

    sync.getTask = function (handle) {

      return this.registered_tasks[handle] || false;
    };

    /**
     * Add dependency to Task
     *
     */

    sync.addDependency = function (handle, dependency_handle) {

      var task = this.getTask(handle);
      task && task.dependencies.push(dependency_handle);
    };

    /**
     * resolve dependencies
     *
     * @param  {[type]} handles   [description]
     * @return {[type]}           [description]
     */

    sync.resolve_dependencies = function (handles) {

      var queue = this.queue;

      handles.forEach(function (handle, index, array) {

        if (0 <= queue.indexOf(handle)) return;

        var task = sync.getTask(handle);

        if (task && task.dependencies.length) sync.resolve_dependencies(task.dependencies);

        queue.push(handle);
      });
    };

    /**
     * get the queued_tasks tasks
     *
     * @return {[type]} [description]
     */

    sync.create_queue = function () {

      this.queue = [];
      this.resolve_dependencies(this.queued_tasks);

      this.queuesize = this.queue.length;
      return this.queue;
    };

    function humanTimeDuration(microseconds) {

      var str;
      var _seconds = microseconds / 1000;
      var hours = Math.floor(_seconds % 31536000 % 86400 / 3600);
      var minutes = Math.floor(_seconds % 31536000 % 86400 % 3600 / 60);
      var seconds = Math.floor(_seconds % 31536000 % 86400 % 3600 % 60);

      if (_seconds < 60) {
        str = 'less than a minute';
      } else if (hours > 1) {
        str = sfw.sprintf(sfw.__('%1$s hours and %2$s minutes', 'apparelcuts-spreadshirt'), hours, minutes);
      } else if (hours == 1) {
        str = sfw.sprintf(sfw.__('one hour and %1$s minutes', 'apparelcuts-spreadshirt'), minutes);
      } else if (minutes > 1) {
        str = sfw.sprintf(sfw.__('%1$s minutes', 'apparelcuts-spreadshirt'), minutes);
      } else {
        str = sfw.sprintf(sfw.__('one minute and %1$s seconds', 'apparelcuts-spreadshirt'), seconds);
      }

      return str;
    }

    /**
     * start a Synchronization
     *
     * @return {[type]} [description]
     */

    sync.run = function () {

      // stats
      this.tasks_done = [];
      this.tasks_failed = [];
      this.runtime_errors = 0;
      this.start = new Date().getTime();

      this.force_update = $('input#force_update').attr('disabled', true).is(':checked');

      // progessbars
      progressbars.get('primary').reset();

      // create the queue
      this.create_queue();
      this.task_count = this.queue.length;

      //console.log( 'queue', this.queue );

      // tasks will stop when set to true
      this.forceStop = false;

      //
      this.synchronization_process = $.Deferred();

      $('body').addClass('sfw-sync-running');

      this.synchronization_process.always(function () {
        console.log(sync.tasks_done.length + ' Tasks completed.');
        console.log(sync.tasks_failed.length + ' Tasks failed.');
        if (sync.tasks_failed.length) sync.stat(sfw.__('Failed Tasks', 'apparelcuts-spreadshirt'), sfw.sprintf('%1$s of %2$s', sync.tasks_failed.length, sync.task_count), sfw.__('Errors', 'apparelcuts-spreadshirt'));
        $('body').removeClass('sfw-sync-running');
      }).done(function () {

        var duration = humanTimeDuration(new Date().getTime() - sync.start);
        var message = 'Synchronization finished after ' + duration + '.';
        progressbars.get('primary').set(100, message);
        console.log('%c' + message, 'color:white;background:green;');
        $('body').addClass('sfw-sync-success');
        stage.show('<span class="ac ac-shirt-check"></span>');
      }).fail(function (err) {
        var duration = humanTimeDuration(new Date().getTime() - sync.start);
        var message = 'Synchronization failed after ' + duration + '. ' + err.msg;
        progressbars.get('primary').set(100, err.msg || '');
        console.error(message);
        $('body').addClass('sfw-sync-failed');
        stage.show('<span class="ac ac-shirt-o"></span>');
      });

      // kick off the queue
      this.runTask();

      return this.synchronization_process.promise();
    };

    sync.stop = function () {
      var _this3 = this;

      if (this.forceStop) {
        this.synchronization_process.reject({ error: 'stopped-manually', msg: sfw.__('Synchronization stopped manually', 'apparelcuts-spreadshirt') });
        return true;
      } else {
        sfw.api({
          route: 'did-sync'
        }).always(function () {
          return _this3.synchronization_process.resolve();
        });
      }
    };

    sync.stopped = function () {

      if (this.forceStop) {
        sync.stop();
        return true;
      }

      return false;
    };

    /**
     * run the next Task from the queue
     */

    sync.runTask = function () {

      if (this.queue.length === 0) {
        sync.stop();
        return;
      }

      if (this.stopped()) {
        return;
      }

      var handle = this.queue.shift(),
          task = this.getTask(handle),
          progress = progressbars.get('primary'),
          task_progress = progressbars.get('secondary');

      var task_deferred = $.Deferred(function (deferred) {

        // check if task is valid
        if (!task) return deferred.reject({ error: 'task-not-found', msg: 'Skipping Task "' + handle + '". Task not found.' });

        // check if task is valid
        if ('function' !== typeof task.callback) return deferred.reject({ error: 'task-not-callable', msg: 'Skipping Task "' + handle + '". Callback is not a function.' });

        // check if all dependencies were met
        var failed_dependency = false;
        task.dependencies.forEach(function (dependency_handle) {
          // stop task runner
          if (-1 === sync.tasks_done.indexOf(dependency_handle)) failed_dependency = dependency_handle;
        });

        if (failed_dependency) return deferred.reject({ error: 'missing-task-dependency', msg: 'Skipping Task "' + handle + '". Missing dependency "' + failed_dependency + '"' });

        // reset subprogress
        progress.setLabel(task.label);
        task_progress.reset();

        console.log('%cStarting Task "' + handle + '"…', 'color:#bbb;');

        // callback must return promise
        // callback will receive the task_progress instance, so it can modify the secondary progressbar
        return task.callback.call(null, task_progress, sync).then(function (d) {
          return deferred.resolve(d);
        }, function (e) {
          return deferred.reject(e);
        });
      });

      // success
      task_deferred.done(function (errors) {

        sync.tasks_done.push(handle);

        errors = errors || [];

        if (errors.length) {
          sync.stat(handle, errors.length, sfw.__('Errors', 'apparelcuts-spreadshirt'));
          console.error('Finished Task "' + handle + '" with errors…', errors);
        } else {
          console.log('%cFinished Task "' + handle + '" without errors…', 'color:#bbb;');
        }
      });

      // following tasks will not be executed if they depend on this task
      task_deferred.fail(function (e) {
        sync.tasks_failed.push(handle);
        console.error('Task failed: ' + handle, e);
      });

      // always continue with the next task
      task_deferred.always(function () {
        task_progress.set(100, '');
        progress.set(sync.tasks_failed.length + sync.tasks_done.length / sync.queuesize * 100);
        sync.screen.stage.reset();
        sync.runTask();
      });
    }; // end runTask


    sync.dataStorage = {};

    sync.get = function (key, defaults) {

      if (defaults === undefined) defaults = false;

      return this.dataStorage[key] || defaults;
    };
    sync.set = function (key, value) {
      this.dataStorage[key] = value;
    };
  } // -- end function plugin

  // register
  wp.hooks.addAction('sfw.register.plugins', 'sfw.plugin.sync', Plugin);
})(); // -- end anon

;(function () {
  function Plugin(sfw, $) {

    var sync = sfw.sync;

    /**
     * InventoryTask Helper
     *
     * @param  {[type]} args     [description]
     * @param  {[type]} sync     [description]
     * @param  {[type]} progress [description]
     * @return {[type]}          [description]
     */

    sfw.InventoryTask = function (args, sync, progress) {

      var self = this;

      this.deferred = $.Deferred();

      // defaults
      this.args = $.extend({
        offset: 0,
        limit: 500,
        url: false,
        dataStorage: false,
        root: false,
        label_log: sfw.__('%1$s Items found', 'apparelcuts-spreadshirt'),
        stat_group: false
      }, args);

      // collect data here
      this.data = [];
      this.errors = [];

      // bail
      if (!this.args.url) return this.deferred.reject({ error: 'missing-url', msg: 'No proper url given.' });

      this.request = function () {

        if (sync.stopped()) return;

        var request_args = {
          method: 'get',
          url: self.args.url,
          data: {
            fullData: 'true',
            offset: self.args.offset,
            limit: self.args.limit,
            noCache: true
          }
        };

        sfw.spreadshirt(request_args).done(function (data) {

          // save
          var newdata = self.args.root ? data[self.args.root] : data;
          self.data = self.data.concat(newdata);

          self.args.stat_group && sync.stat('Spreadshirt', self.data.length, self.args.stat_group);

          // offset ++
          self.args.offset += self.args.limit;

          if (self.args.offset <= data.count) {
            progress.set(self.args.offset / data.count * 100);
            // continue
            setTimeout(self.request, sfw.get('remote_timeout', 500));
          } else {
            // save in dataStorage
            if (self.args.dataStorage) sync.set(self.args.dataStorage, self.data);

            console.log(sfw.sprintf(self.args.label_log, self.data.length));

            // resolve
            self.deferred.resolve(self.errors, self.data);
          }
        }).fail(function (xhr) {
          self.deferred.reject({ error: 'request', msg: sfw.extractErrorMsg(xhr), data: xhr });
        });
      };

      this.request();

      return this.deferred.promise();
    };

    /**
     * SyncingTask Helper
     *
     * @param  {[type]} args     [description]
     * @param  {[type]} sync     [description]
     * @param  {[type]} progress [description]
     * @return {[type]}          [description]
     */

    sfw.SyncingTask = function (args, sync, progress) {
      var _this4 = this;

      var self = this;

      this.deferred = $.Deferred();

      // defaults
      this.args = $.extend({
        // arry - items with id property or ids
        data: [],
        // string - the entity identifies the item as article, design etc.
        entity: null

      }, args);

      // store ids
      this.queue = [];

      // copy data
      $.each(this.args.data, function (index, value) {

        if ((typeof value === 'undefined' ? 'undefined' : _typeof(value)) === "object" && value.id) _this4.queue.push(value);else _this4.queue.push({ id: value });
      });

      this.total = this.queue.length;
      this.errors = [];

      // bail
      if (!this.queue.length) return this.deferred.resolve(self.errors);

      this.request = function () {

        if (sync.stopped()) return;

        var current_item = self.queue.shift();
        var spreadshirt_id = current_item.id;

        progress.setLabel(sfw.__('Syncing...', 'apparelcuts-spreadshirt') + ' ' + spreadshirt_id);

        try {

          if (current_item.resources) {

            for (var index in current_item.resources) {

              if (current_item.resources[index].type == 'preview') sync.screen.stage.show($('<img/>').attr('src', current_item.resources[index].href + ',width=50,height=50'));
            }
          }
        } catch (e) {}

        var data = {
          entity: self.args.entity,
          spreadshirt_id: spreadshirt_id,
          force_update: sync.force_update
        };

        sfw.api({
          route: 'sync-item',
          data: data
        }).fail(function (xhr) {
          self.errors.push(xhr);
          console.log(sfw.extractErrorMsg(xhr));
        }).always(function () {

          if (self.queue.length) {
            progress.set((self.total - self.queue.length) / self.total * 100);
            // continue
            setTimeout(self.request, sfw.get('local_timeout', 500));
          } else {
            // resolve
            self.deferred.resolve(self.errors);
          }
        });
      };

      this.request();

      return this.deferred.promise();
    };

    /**
     * InventoryTask Helper
     *
     * @param  {[type]} args     [description]
     * @param  {[type]} sync     [description]
     * @param  {[type]} progress [description]
     * @return {[type]}          [description]
     */

    sfw.EntityInventoryTask = function (args, sync, progress) {

      var self = this;

      this.deferred = $.Deferred();

      // defaults
      this.args = $.extend({
        // string - the entity identifies the item as article, design etc.
        entity: null,
        offset: 0,
        limit: 500,
        dataStorage: false,
        label_log: sfw.__('%1$s Items found', 'apparelcuts-spreadshirt'),
        stat_group: false
      }, args);

      // collect data here
      this.data = [];
      this.errors = [];

      this.request = function () {

        if (sync.stopped()) return;

        var request_args = {
          route: 'entity-list',
          data: {
            offset: self.args.offset,
            limit: self.args.limit,
            entity: self.args.entity
          }
        };

        sfw.api(request_args).done(function (data) {

          // save
          var newdata = data.items;
          self.data = self.data.concat(newdata);
          self.args.stat_group && sync.stat('Wordpress', self.data.length, self.args.stat_group);

          // offset ++
          self.args.offset += self.args.limit;

          if (data.results == 0 || data.results < self.args.limit) {

            // save in dataStorage
            if (self.args.dataStorage) sync.set(self.args.dataStorage, self.data);

            console.log(sfw.sprintf(self.args.label_log, self.data.length));

            // resolve
            self.deferred.resolve(self.errors, self.data);
          } else {

            if (data.count) {
              progress.set(self.args.offset / data.count * 100);
            }

            // continue
            setTimeout(self.request, sfw.get('local_timeout', 500));
          }
        }).fail(function (xhr) {
          self.deferred.reject({ error: 'request', msg: sfw.extractErrorMsg(xhr), data: xhr });
        });
      };

      this.request();

      return this.deferred.promise();
    };

    /**
     * UpdateableEntityTask Helper
     *
     * @param  {[type]} args     [description]
     * @param  {[type]} sync     [description]
     * @param  {[type]} progress [description]
     * @return {[type]}          [description]
     */

    sfw.UpdateableEntityTask = function (args, sync, progress) {

      var self = this;

      this.deferred = $.Deferred();

      // defaults
      this.args = $.extend({
        // list of existing entity item
        database_items: [],
        // spreadshirt items
        remote_items: [],
        dataStorage: false,
        stat_group: false
      }, args);

      this.errors = [];

      function getRemoteItem(spreadshirt_id) {

        for (var i in self.args.remote_items) {
          if (self.args.remote_items[i].id == spreadshirt_id) return self.args.remote_items[i];
        }
        return false;
      }

      function getDatabaseItem(spreadshirt_id) {

        for (var i in self.args.database_items) {
          if (self.args.database_items[i]['spreadshirt-id'] == spreadshirt_id) return self.args.database_items[i];
        }
        return false;
      }

      var result = {
        requires_action: [],
        remove_required: [],
        requires_no_action: 0,
        update_required: 0,
        update_forced: 0,
        create_required: 0
      };

      // check for items that are new or need an update
      for (var i in this.args.remote_items) {
        var remote_item = this.args.remote_items[i];

        var database_item = getDatabaseItem(remote_item.id);

        if (!database_item) {
          result.create_required++;
          result.requires_action.push(remote_item);
        } else if (database_item.expired) {
          result.update_required++;
          result.requires_action.push(remote_item);
        } else if (sync.force_update) {
          result.update_forced++;
          result.requires_action.push(remote_item);
        } else {
          result.requires_no_action++;
        }
      }

      // check for items that were removed from the remote shop
      for (var i in this.args.database_items) {
        var database_item = this.args.database_items[i];

        var remote_item = getRemoteItem(database_item['spreadshirt-id']);

        if (!remote_item) {
          result.remove_required.push(database_item);
        }
      }

      // save in dataStorage
      if (self.args.dataStorage) sync.set(self.args.dataStorage, result);

      if (self.args.stat_group) {
        sync.stat(sfw.__('New', 'apparelcuts-spreadshirt'), result.create_required, self.args.stat_group);
        sync.stat(sfw.__('Update', 'apparelcuts-spreadshirt'), result.update_required, self.args.stat_group);
        if (sync.force_update) sync.stat(sfw.__('Update (forced)', 'apparelcuts-spreadshirt'), result.update_forced, self.args.stat_group);else sync.stat(sfw.__('No Action', 'apparelcuts-spreadshirt'), result.requires_no_action, self.args.stat_group);

        sync.stat(sfw.__('Removed', 'apparelcuts-spreadshirt'), result.remove_required.length, self.args.stat_group);
      }

      return this.deferred.resolve(this.errors, result);
    };

    /**
     * Trashes a list of posts
     *
     * @param  {[type]} args     [description]
     * @param  {[type]} sync     [description]
     * @param  {[type]} progress [description]
     * @return {[type]}          [description]
     */

    sfw.TrashPostsTask = function (args, sync, progress) {

      var self = this;

      this.deferred = $.Deferred();

      // defaults
      this.args = $.extend({
        posts: [], // [ { id : 5 }, ... ]
        limit: 10
      }, args);

      // collect data here
      this.errors = [];

      this.request = function () {
        var _this5 = this;

        if (sync.stopped()) return;

        var queue = [];
        for (var i = 0; i < self.args.limit; i++) {
          if (self.args.posts.length) queue.push(self.args.posts.shift().id);
        }

        var request_args = {
          route: 'trash-posts',
          data: {
            posts: queue
          }
        };

        sfw.api(request_args).fail(function (xhr) {
          self.errors.push(xhr);
        }).always(function () {

          if (self.args.posts.length == 0) {
            self.deferred.resolve(_this5.errors);
            return;
          } else {
            // continue
            setTimeout(self.request, sfw.get('local_timeout', 500));
          }
        });
      };

      this.request();

      return this.deferred.promise();
    };
  } // -- end function plugin

  // register
  wp.hooks.addAction('sfw.sync.init', 'sfw.sync.tasks.proto', Plugin);
})(); // -- end anon

;(function () {
  function Plugin(sfw, $) {

    var sync = sfw.sync;

    sync.registerTask('db-articles', {

      label: sfw.__('Syncing Database Articles', 'apparelcuts-spreadshirt'),

      dependencies: [],

      callback: function callback(progress, sync) {

        return sfw.EntityInventoryTask({
          entity: 'article',
          dataStorage: 'db-articles',
          label_log: sfw.__('%1$s Articles found', 'apparelcuts-spreadshirt'),
          stat_group: sfw.__('Articles', 'apparelcuts-spreadshirt')
        }, sync, progress);
      }

    });

    sync.registerTask('articles', {

      label: sfw.__('Syncing Shop Articles', 'apparelcuts-spreadshirt'),

      dependencies: [],

      callback: function callback(progress, sync) {

        return sfw.InventoryTask({
          url: sfw.get('shop').articleCategories.href + '/510/articles',
          dataStorage: 'articles',
          root: 'articles',
          label_log: sfw.__('%1$s Articles found', 'apparelcuts-spreadshirt'),
          stat_group: sfw.__('Articles', 'apparelcuts-spreadshirt')
        }, sync, progress);
      }

    });

    sync.registerTask('prepare-article-sync', {

      label: sfw.__('Preparing Article Updates', 'apparelcuts-spreadshirt'),

      dependencies: ['db-articles', 'articles'],

      callback: function callback(progress, sync) {

        return sfw.UpdateableEntityTask({
          database_items: sync.get('db-articles', []),
          remote_items: sync.get('articles', []),
          dataStorage: 'prepared-articles',
          stat_group: sfw.__('Articles', 'apparelcuts-spreadshirt')
        }, sync, progress);
      }

    });

    sync.enqueueTask('sync-articles', {

      label: sfw.__('Syncing Shop Articles', 'apparelcuts-spreadshirt'),

      dependencies: ['prepare-article-sync'],

      callback: function callback(progress, sync) {

        return sfw.SyncingTask({
          data: sync.get('prepared-articles').requires_action,
          entity: 'article'
        }, sync, progress);
      }

    });

    /**
     * Trash Articles that are not in the Shop Listing or have an unavailable Producttype
     *
     * Unfortunately there is currently no better way to check if an article is unavailable
     *
     * @type {[type]}
     */

    sync.enqueueTask('article-cleanup', {

      label: sfw.__('Trashing Shop Articles', 'apparelcuts-spreadshirt'),

      dependencies: ['prepare-article-sync', 'prepare-producttype-sync'],

      callback: function callback(progress, sync) {

        var database_articles = sync.get('db-articles'),
            removed_producttypes = sync.get('prepared-producttypes').remove_required,
            removed_articles = sync.get('prepared-articles').remove_required,
            trash_posts_assoc = {},
            promise = $.Deferred(),
            errors = [];

        // test
        //removed_producttypes.push({ id : 1202 });

        // remove expired articles
        // index by id to eliminate duplicates
        if (removed_articles && removed_articles.length) {

          for (var i in removed_articles) {

            if (removed_articles[i].post_status == 'publish') trash_posts_assoc[removed_articles[i].id] = removed_articles[i];
          }
        }

        // remove articles by expired producttypes
        // index by id to eliminate duplicates
        if (removed_producttypes && removed_producttypes.length) {

          for (var i in removed_producttypes) {

            var removed_articles = sfw.filterObjectList(database_articles, 'producttype-id', removed_producttypes[i].id);

            // index by id to eliminate duplicates
            if (removed_articles && removed_articles.length) {
              for (var i in removed_articles) {
                if (removed_articles[i].post_status == 'publish') trash_posts_assoc[removed_articles[i].id] = removed_articles[i];
              }
            }
          }
        }

        if (Object.keys(trash_posts_assoc).length) {

          var trash_posts = [];
          for (var i in trash_posts_assoc) {
            trash_posts.push(trash_posts_assoc[i]);
          }sfw.TrashPostsTask({
            posts: trash_posts
          }, sync, progress).done(function (errors) {
            return promise.resolve(errors);
          }).fail(function (err) {
            return promise.reject(err);
          });
        } else {
          return promise.resolve(errors);
        }

        return promise;
      }

    });
  } // -- end function plugin

  // register
  wp.hooks.addAction('sfw.sync.init', 'sfw.sync.tasks.articles', Plugin);
})(); // -- end anon

;(function () {
  function Plugin(sfw, $) {

    var sync = sfw.sync;

    sync.registerTask('db-producttypes', {

      label: sfw.__('Syncing Database Articles', 'apparelcuts-spreadshirt'),

      dependencies: [],

      callback: function callback(progress, sync) {

        return sfw.EntityInventoryTask({
          entity: 'producttype',
          dataStorage: 'db-producttypes',
          label_log: sfw.__('%1$s Producttypes found', 'apparelcuts-spreadshirt'),
          stat_group: sfw.__('Producttypes', 'apparelcuts-spreadshirt')
        }, sync, progress);
      }

    });

    sync.registerTask('producttypes', {

      label: sfw.__('Syncing Shop Producttypes', 'apparelcuts-spreadshirt'),

      dependencies: [],

      callback: function callback(progress, sync) {

        return sfw.InventoryTask({
          url: sfw.get('shop').productTypes.href,
          dataStorage: 'producttypes',
          root: 'productTypes',
          label_log: sfw.__('%1$s ProductTypes found', 'apparelcuts-spreadshirt'),
          stat_group: sfw.__('Producttypes', 'apparelcuts-spreadshirt')
        }, sync, progress);
      }

    });

    sync.registerTask('prepare-producttype-sync', {

      label: sfw.__('Preparing Producttype Updates', 'apparelcuts-spreadshirt'),

      dependencies: ['db-producttypes', 'producttypes'],

      callback: function callback(progress, sync) {

        return sfw.UpdateableEntityTask({
          database_items: sync.get('db-producttypes', []),
          remote_items: sync.get('producttypes', []),
          dataStorage: 'prepared-producttypes',
          stat_group: sfw.__('Producttypes', 'apparelcuts-spreadshirt')
        }, sync, progress);
      }

    });

    sync.registerTask('sync-producttypes', {

      label: sfw.__('Syncing Shop ProductTypes', 'apparelcuts-spreadshirt'),

      dependencies: ['prepare-producttype-sync'],

      callback: function callback(progress, sync) {

        return sfw.SyncingTask({
          data: sync.get('prepared-producttypes').requires_action,
          entity: 'producttype'
        }, sync, progress);
      }

    });
  } // -- end function plugin

  // register
  wp.hooks.addAction('sfw.sync.init', 'sfw.sync.tasks.producttypes', Plugin);
})(); // -- end anon

;(function () {
  function Plugin(sfw, $) {

    var sync = sfw.sync;

    sync.registerTask('designs', {

      label: sfw.__('Syncing Shop Designs', 'apparelcuts-spreadshirt'),

      dependencies: [],

      callback: function callback(progress, sync) {

        return sfw.InventoryTask({
          url: sfw.get('shop').designCategories.href + '/510/designs',
          dataStorage: 'designs',
          root: 'designs',
          label_log: sfw.__('%1$s Designs found', 'apparelcuts-spreadshirt'),
          stat_group: sfw.__('Designs', 'apparelcuts-spreadshirt')
        }, sync, progress);
      }

    });

    sync.registerTask('sync-designs', {

      label: sfw.__('Syncing Shop Designs', 'apparelcuts-spreadshirt'),

      dependencies: ['designs'],

      callback: function callback(progress, sync) {

        return sfw.SyncingTask({
          data: sync.get('designs', []),
          entity: 'design'
        }, sync, progress);
      }

    });

    sync.registerTask('printtypes', {

      label: sfw.__('Syncing Shop PrintTypes', 'apparelcuts-spreadshirt'),

      dependencies: [],

      callback: function callback(progress, sync) {

        return sfw.InventoryTask({
          url: sfw.get('shop').productTypes.href,
          dataStorage: 'printtypes',
          root: 'printTypes',
          label_log: sfw.__('%1$s PrintTypes found', 'apparelcuts-spreadshirt'),
          stat_group: sfw.__('Printtypes', 'apparelcuts-spreadshirt')
        }, sync, progress);
      }

    });

    sync.registerTask('sync-printtypes', {

      label: sfw.__('Syncing Shop PrintTypes', 'apparelcuts-spreadshirt'),

      dependencies: ['printtypes'],

      callback: function callback(progress, sync) {

        return sfw.SyncingTask({
          data: sync.get('printtypes', []),
          entity: 'printtype'
        }, sync, progress);
      }

    });
  } // -- end function plugin

  // register
  wp.hooks.addAction('sfw.sync.init', 'sfw.sync.tasks', Plugin);
})(); // -- end anon
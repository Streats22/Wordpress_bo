(function ($) {
	'use strict';
	$(function () {
		cloudStorageFunctions.set(); 
		var wf_tab_view =
		{
			Set: function () {
				this.subTab();
				this.accordion();
				var wf_nav_tab = $('.wf-tab-head .nav-tab');
				if (wf_nav_tab.length > 0) {
					wf_nav_tab.click(function () {
						var wf_tab_hash = $(this).attr('href');
						wf_nav_tab.removeClass('nav-tab-active');
						$(this).addClass('nav-tab-active');
						wf_tab_hash = wf_tab_hash.charAt(0) == '#' ? wf_tab_hash.substring(1) : wf_tab_hash;
						var wf_tab_elm = $('div[data-id="' + wf_tab_hash + '"]');
						$('.wf-tab-content').hide();
						if (wf_tab_elm.length > 0 && wf_tab_elm.is(':hidden')) {
							wf_tab_elm.stop(true, true).fadeIn();
						}
					});
					$(window).on('hashchange', function (e) {
						var location_hash = window.location.hash;
						if (location_hash != "") {
							wf_tab_view.showTab(location_hash);
						}
					}).trigger('hashchange');

					var location_hash = window.location.hash;
					if (location_hash != "") {
						wf_tab_view.showTab(location_hash);
					} else {
						wf_nav_tab.eq(0).click();
					}
				}
			},
			showTab: function (location_hash) {
				var wf_tab_hash = location_hash.charAt(0) == '#' ? location_hash.substring(1) : location_hash;
				if (wf_tab_hash != "") {
					var wf_tab_hash_arr = wf_tab_hash.split('#');
					wf_tab_hash = wf_tab_hash_arr[0];
					var wf_tab_elm = $('div[data-id="' + wf_tab_hash + '"]');
					if (wf_tab_elm.length > 0 && wf_tab_elm.is(':hidden')) {
						$('a[href="#' + wf_tab_hash + '"]').click();
						if (wf_tab_hash_arr.length > 1) {
							var wf_sub_tab_link = wf_tab_elm.find('.wf_sub_tab');
							if (wf_sub_tab_link.length > 0) /* subtab exists  */ {
								var wf_sub_tab = wf_sub_tab_link.find('li[data-target=' + wf_tab_hash_arr[1] + ']');
								wf_sub_tab.click();
							}
						}
					}
				}
			},
			subTab: function () {
				$('.wf_sub_tab li').click(function () {
					var trgt = $(this).attr('data-target');
					var prnt = $(this).parent('.wf_sub_tab');
					var ctnr = prnt.siblings('.wf_sub_tab_container');
					prnt.find('li a').css({ 'color': '#0073aa', 'cursor': 'pointer' });
					$(this).find('a').css({ 'color': '#ccc', 'cursor': 'default' });
					ctnr.find('.wf_sub_tab_content').hide();
					ctnr.find('.wf_sub_tab_content[data-id="' + trgt + '"]').fadeIn();
				});
				$('.wf_sub_tab').each(function () {
					var elm = $(this).children('li').eq(0);
					elm.click();
				});
			},
			accordion : function() {
			
				if (jQuery('.wt-migrator-accordion-tab').hasClass('accordion-active')) {
					jQuery('.wt-migrator-accordion-tab.accordion-active').find('.wt-migrator-accordion-content').slideDown(0);
				}
				jQuery(document).on('click', '.wt-migrator-accordion-tab > a', function (e) {
					e.preventDefault();
					var $this = jQuery(this);
					if ($this.next().hasClass('accordion-active')) {
						$this.removeClass('accordion-active');
						$this.next().removeClass('accordion-active');
						$this.closest('.wt-migrator-accordion-tab').removeClass('accordion-active');
						$this.next().slideUp(350);
					} else {
						// $this.parent().parent().find('.wt-migrator-accordion-content').removeClass('accordion-active');
						// $this.parent().parent().find('.wt-migrator-accordion-content').slideUp(350);
						// $this.parent().parent().find('.wt-migrator-accordion-tab > a').removeClass('accordion-active');
						// $this.parent().parent().find('.wt-migrator-accordion-tab').removeClass('accordion-active');
						$this.toggleClass('accordion-active');
						$this.closest('.wt-migrator-accordion-tab').toggleClass('accordion-active');
						$this.next().toggleClass('accordion-active');
						$this.next().slideToggle(350);
			
					}
				});
				
			}
		}
		wf_tab_view.Set();

	});
})(jQuery);

var wf_progress_bar =
{
	Set: function (vl, elm, inner_text, no_animate) {
		if (elm) {
			var bar_inner = elm.find('.wf_progress_bar_inner');
			if (inner_text) {
				elm.find('.wf_progress_bar_label').html(inner_text);
			}
		} else {
			var bar_inner = jQuery('.wf_progress_bar_inner');
		}
		bar_inner.parent('.wf_progress_bar').show();
		if (no_animate || vl == 0) {
			bar_inner.css({ 'width': (vl + '%') }).html(vl + '%').attr({ 'data-val': vl });
		} else {
			bar_inner.stop(true, true).animate({ 'width': (vl + '%') }, 200).html(vl + '%').attr({ 'data-val': vl });
		}
	},
	updateLabel: function (elm, inner_text) {
		if (elm) {
			var bar_inner = elm.find('.wf_progress_bar_inner');
			if (inner_text) {
				elm.find('.wf_progress_bar_label').html(inner_text);
			}
		}
	},
	Reset: function (vl, elm, inner_text) {
		this.Set(vl, elm, inner_text, true);
	}
}
var popup_handler =
{
	 hidePopup:function()
            {
                    jQuery('.wt_mgdp_popup_close').click();
            },
        hide_export_info_box:function()
            {
                    jQuery('.wt_mgdp_loader_info_box').hide();
            },
}

var wp_migration_duplicator_notify_msg =
{
	error: function (message) {
		var er_elm = jQuery('<div class="notify_msg" style="background:#f8d7da; border:solid 1px #f5c6cb; color:  #721c24">' + message + '</div>');
		this.setNotify(er_elm);
	},
	success: function (message) {
		var suss_elm = jQuery('<div class="notify_msg" style="background:#d4edda; border:solid 1px #c3e6cb; color: #155724;">' + message + '</div>');
		this.setNotify(suss_elm);
	},
	setNotify: function (elm) {
		jQuery('body').append(elm);
		elm.stop(true, true).animate({ 'opacity': 1, 'top': '50px' }, 1000);
 
                jQuery('body').click(function(){
                        elm.animate({ 'opacity': 0, 'top': '100px' }, 1000, function () {
                        elm.remove();
                    });
                });
	}
}
var cloudStorageFunctions = {
	loader:'',	
	authenticateCloudStorage: function ( element) {
		
		currentElement = element;
		var cloudStorage = currentElement.find('option:selected').val();
		this.loader = currentElement.closest('.wt-migrator-select-container').find('.spinner');
		var optionType = currentElement.attr('data-option-type');
		var targetElement = jQuery('.wt_mgdb_'+optionType+'_option_' + cloudStorage );
		var commonElement = jQuery('.child-wt_mgdb_'+optionType+'_option');
		commonElement.slideUp(100);
		this.loader.show().css({ 'visibility': 'visible' });
		var ajaxurl = wtMigratorObject.ajax_url;
		var data = {
			'action'		: 'wp_mgdp_check_authentication',
			'cloud_storage' :  	cloudStorage,
			'_wpnonce'		: 	wtMigratorObject.nonce
		};
		jQuery.ajax({
			url: ajaxurl,
			type: 'POST',
			data: data,
			dataType: 'json',
			success: function (response) {
				jQuery('.wt-migrator-cloud-import-file.wt-migrator-select2-single').select2();

				settingsElement = targetElement.find('.wt-migrator-cloud-settings-link');
				settingsText = settingsElement.text();
				disabled = true;
				if (response.success === true) {
					
					targetElement.addClass('wt-migrator-authenticated');
					disabled = false;
					if( optionType === "import") {
						cloudStorageFunctions.populateBackups(cloudStorage ,targetElement);
					} else {
						targetElement.slideDown(500);
						cloudStorageFunctions.loader.hide().css({ 'visibility': 'hidden' });
					}
					
				}
				else {
					targetElement.addClass('wt-migrator-disconnected');
					targetElement.slideDown(500);
					cloudStorageFunctions.loader.hide().css({ 'visibility': 'hidden' });
				}

				targetElement.find('.wt-migrator-file').prop('disabled', disabled);
				
			},
			error: function () {
				targetElement.addClass('wt-migrator-disconnected');
				targetElement.slideDown(500);
				cloudStorageFunctions.loader.hide().css({ 'visibility': 'hidden' });
			}
		});
		
	},
	set : function() {
		jQuery('select[name="wt_mgdb_export_option"],select[name="wt_mgdb_import_option"]').on('change', function (e) {
			e.preventDefault();
			cloudStorageFunctions.authenticateCloudStorage( jQuery(this) );
		});
		jQuery('.wt-migrator-cloud-import-file').on('select2:select', function (e) {
			var cloudlocation = jQuery(this).attr('data-hidden-name');
			var data = e.params.data;
			jQuery('input[type="hidden"][name="'+cloudlocation+'"]').val(data.id);

		});
	},
	populateBackups: function( cloudStorage,targetElement ) {

		var ajaxurl = wtMigratorObject.ajax_url;
		var data = {
			'action'		: 'wp_mgdp_populate_cloud_files',
			'cloud_storage' :  	cloudStorage,
			'_wpnonce'		: 	wtMigratorObject.nonce
		};
		jQuery.ajax({
			url: ajaxurl,
			type: 'POST',
			data: data,
			dataType: 'json',
			success: function (response) {
				if (response.success === true) {
					jQuery('.wt-migrator-cloud-import-file').html('');
					jQuery('.wt-migrator-cloud-import-file').append('<option value="-1">'+wtMigratorObject.messages.select_backup+'</option>');
					response.data.forEach(function( item ) {
						jQuery('.wt-migrator-cloud-import-file').append('<option value="' + item.file + '">' + item.name + '</option>')
						targetElement.slideDown(500);
					});
				}
				else {
					jQuery('.wt-migrator-cloud-import-file').html('');
					jQuery('.wt-migrator-cloud-import-file').append('<option value="-1">'+wtMigratorObject.messages.no_backups+'</option>');
					targetElement.slideDown(500);
				}
				cloudStorageFunctions.loader.hide().css({ 'visibility': 'hidden' });
			},
			error: function () {
				jQuery('.wt-migrator-cloud-import-file').html('');
				jQuery('.wt-migrator-cloud-import-file').append('<option value="-1">'+wtMigratorObject.messages.no_backups+'</option>');
				targetElement.slideDown(500);
				cloudStorageFunctions.loader.hide().css({ 'visibility': 'hidden' });
			}
		});
	} 
	
}
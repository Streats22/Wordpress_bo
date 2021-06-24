<?php if ( ! defined( '\ABSPATH' ) ) exit;


  /**
  * This Class helps to iterate through api data in the style of the wordpress post loop
  *
  * @param array $nodes - a collection to loop through
  * @since 1.0.0
  */

  class Sfw_Node_Loop {




    public $current_node_index = -1;




    private $nodes, $node_count;




    /**
    * Save $nodes and count as property
    *
    * @param array $nodes
    * @since 1.0.0
    */

    function __construct( $nodes ) {

      $this->nodes = $nodes;
      $this->node_count = count( $nodes );
    }




    /**
    * Run the Loop, automatically starts and stops
    *
    * use it like "while( have_posts() ) { ... }  ""
    *
    * @param array $nodes
    * @return bool
    * @since 1.0.0
    */

    function have_nodes() {

      if ( $this->next() < $this->count() ) {
				return true;
			}

			$this->rewind();
      return false;
    }




    /**
    * if the loop is currently running
    *
    * @return bool
    * @since 1.0.0
    */

    function in_the_loop() {
      return -1 != $this->current_node_index;
    }




    /**
    * rises the current index by one
    *
    * @return int current index, starting at zero
    * @since 1.0.0
    */
    function next() {
      return ++$this->current_node_index;
    }




    /**
    * rewind the loop, if the loop is running it will immediately start from the beginning
    *
    * @return int current index, starting at zero
    * @since 1.0.0
    */

    function rewind() {

      $this->current_node_index = -1;
    }




    /**
    * returns the current node
    *
    * @return mixed
    * @since 1.0.0
    */

    function current_node() {
      return $this->in_the_loop()
        ? $this->nodes[ $this->current_node_index]
        : false;
    }




    /**
    * return the node count
    *
    * @return int current index, starting at zero
    * @since 1.0.0
    */
    function count() {
      return $this->node_count;
    }





  }

<?php

class Manage extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->load->library('session');
		$this->load->helper('priority');		
		$this->load->library('Grocery_crud');
		
	}
	
	function index()
	{
		//$output = (object)array('output' => '' , 'js_files' => array() , 'css_files' => array());
		//$this->load->view('crud',$output);
		redirect('manage/downloads');
	}	
	
	function downloads() {
		try{
			$crud = new Grocery_crud();
			$crud->set_table('downloads');
			$crud->set_subject('Download');

			$crud->set_allowed_types('pdf|xls|xlsx|doc|docx|zip|rar');
			$crud->set_field_upload('file', 'assets/downloads/');
			$crud->columns('title', 'category_id', 'file', 'total_downloads', 'move_up_down');
			$crud->unset_fields('total_downloads', 'section');
			$crud->change_field_type('last_updated_on', 'hidden', date('Y-m-d H:i:s'));
			$crud->change_field_type('priority', 'hidden');
			$crud->set_relation('category_id', 'categories', 'category_name');
			$crud->display_as('category_id', 'Category');
			
			$crud->callback_before_insert('set_download_priority');
			$crud->callback_column('move_up_down', array($this, 'populate_up_down'));
			//code for re-reodering
			$this->session->set_userdata('primary_key', 'id');
			$this->session->set_userdata('callableAction', base_url(). 'manage/updateGroupPosition/downloads/category_id');
			$crud->set_js("manage/dragdrop_js");
			
			$crud->order_by('category_id, priority', 'asc');
			
			$output = $crud->render();
			$data = array();
			$data['current_view'] = 'Downloads';
			$output->data=$data;
			$this->load->view('crud.php',$output);
				
		}catch(Exception $e){
			show_error($e->getMessage().' --- '.$e->getTraceAsString());
		}		
	}

	function categories() {
		try{
			$crud = new Grocery_crud();
			$crud->set_table('categories');
			$crud->set_subject('Category');
		
			$crud->unset_jquery();		
			$output = $crud->render();
			$data = array();
			$data['current_view'] = 'Categories';
			$output->data=$data;
			$this->load->view('crud.php',$output);
	
		}catch(Exception $e){
			show_error($e->getMessage().' --- '.$e->getTraceAsString());
		}
	}	
	
	function updatePosition($table, $sourceId, $distance, $direction) {

		$this->load->library('Priority_manager');
		$manager = new Priority_manager();
		$manager->setTable($table);
		$manager->setPriorityField('priority');

		switch ($direction) {
			case 'up' :
				$manager->moveUpBy($sourceId, $distance);
				break;
			case 'down' :
				$manager->moveDownBy($sourceId, $distance);
				break;
			case 'top' :
				$manager->moveToTop($sourceId);
				break;
			case 'bottom' :
				$manager->moveToBottom($sourceId);
				break;
			case 'default' :
				$manager->moveTo($sourceId, $distance);
				break;
		}
	}

	function updateGroupPosition($table, $group, $sourceId, $distance, $direction) {

		$this->load->library('Priority_manager');
		$manager = new Priority_manager();
		$manager->setTable($table);
		$manager->setGroupField($group);
		$manager->setPriorityField('priority');

		switch ($direction) {
			case 'up' :
				$manager->moveUpBy($sourceId, $distance);
				break;
			case 'down' :
				$manager->moveDownBy($sourceId, $distance);
				break;
			case 'top' :
				$manager->moveToTop($sourceId);
				break;
			case 'bottom' :
				$manager->moveToBottom($sourceId);
				break;
			case 'default' :
				$manager->moveTo($sourceId, $distance);
				break;
		}
	}

	function dragdrop_js() {
		$js = '
				var startPosition;
				var endPosition;
				var itemBeingDragged;
				var allIds = new Array();


				function makeAjaxCall(_url) {
				/* Send the data using post and put the results in a div */
				$.ajax({
				url: _url,
				type: "get",
				success: function(){
				$(".pReload").click();
				makeTableSortable();
	},
				error:function(){
				alert("There was a failure while repositioning the element");
	}
	});
	}

				function moveUp(sourceId) {
				url="' . $this->session->userdata('callableAction') . '/" + sourceId +"/1/up";
						makeAjaxCall(url);
	}

						function moveDown(sourceId) {
						url="' . $this->session->userdata('callableAction') . '/" + sourceId +"/1/down";
								makeAjaxCall(url);
	}
								 
								function moveToTop(sourceId) {
								url="' . $this->session->userdata('callableAction') . '/" + sourceId +"/1/top";
										makeAjaxCall(url);
	}

										function moveToBottom(sourceId) {
										url="' . $this->session->userdata('callableAction') . '/" + sourceId +"/1/bottom";
												makeAjaxCall(url);
	}

												// Return a helper with preserved width of cells
												var fixHelper = function(e, ui) {
												ui.children().each(function() {
												$(this).width($(this).width());
	});
												return ui;
	};

												function makeTableSortable() {
												$("#flex1 tbody").sortable(
												{
												helper: fixHelper,
												cursor : "move",
												create: function(event, ui) {
												allRows = $( "#flex1 tbody" ).sortable({ items: "> tr" }).children();
												for(var i=0; i< allRows.length; i++) {
												var _row = allRows[i];
												_id = _row.attributes["data_id"].value;
												//_id = _id.substr(4);
												allIds.push(_id);
												//console.log("Pushed - " + _id);
	}
	},
												start : function(event, ui) {
												startPosition = ui.item.prevAll().length + 1;
												itemBeingDragged = ui.item.attr("data_id");
	},
												update : function(event, ui) {
												endPosition = ui.item.prevAll().length + 1;
												if(startPosition != endPosition) {
												if(startPosition > endPosition) {
												distance = startPosition - endPosition;
												url="' . $this->session->userdata('callableAction') . '/" + itemBeingDragged +"/" + distance + "/up";
														makeAjaxCall(url);
	} else {
														distance = endPosition - startPosition;
														url="' . $this->session->userdata('callableAction') . '/" + itemBeingDragged +"/" + distance + "/down";
																makeAjaxCall(url);
	}
	}
	}
	})
	}
																 
																window.onload = function() {
																makeTableSortable();
	};';
		header("Content-type: text/javascript");
		echo $js;
	}

	function resetPositions($table, $group_field=FALSE, $group_value=FALSE) {
		$this->load->library('Priority_manager');
		$manager = new Priority_manager();
		$manager->setTable($table);
		$manager->setGroupField($group_field);
		$manager->setPriorityField('priority');
		$manager->rearrangePriorities($group_value);
	}

	public function populate_up_down($value, $row) {
		$primary_key = $this->session->userdata('primary_key');
		$str = "<a href='javascript:moveToTop(\"" . $row->$primary_key . "\")'><img src='" . base_url() . "assets/images/navigate-top-icon.png' height=16px></a>";
		$str .= "<a href='javascript:moveUp(\"" . $row->$primary_key . "\")'><img src='" . base_url() . "assets/images/navigate-up-icon.png' height=16px></a>";
		$str .= "<a href='javascript:moveDown(\"" . $row->$primary_key . "\")'><img src='" . base_url() . "assets/images/navigate-down-icon.png' height=16px></a>";
		$str .= "<a href='javascript:moveToBottom(\"" . $row->$primary_key . "\")'><img src='" . base_url() . "assets/images/navigate-bottom-icon.png' height=16px></a>";
		return $str;
	}

}
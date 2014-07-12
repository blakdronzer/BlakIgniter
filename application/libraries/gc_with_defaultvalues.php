<?php
/**
 * PHP grocery CRUD
 *
 * A Codeigniter library that creates a CRUD automatically with just few lines of code.
 *
 * Copyright © 2010 - 2012  John Skoumbourdis. 
 *
 * LICENSE
 *
 * Grocery CRUD is released with dual licensing, using the GPL v3 (license-gpl3.txt) and the MIT license (license-mit.txt).
 * You don't have to do anything special to choose one license or the other and you don't have to notify anyone which license you are using.
 * Please see the corresponding license file for details of these licenses.
 * You are free to use, modify and distribute this software, but all copyright information must remain.
 *
 * @package    	grocery CRUD
 * @copyright  	Copyright (c) 2010 through 2012, John Skoumbourdis
 * @license    	https://github.com/scoumbourdis/grocery-crud/blob/master/license-grocery-crud.txt
 * @version    	1.3
 * @author     	John Skoumbourdis <scoumbourdisj@gmail.com>
 */

// ------------------------------------------------------------------------
include_once(dirname(__FILE__)."/grocery_crud.php");


/**
 * PHP grocery CRUD - Customized
 *
 * Creates a full functional CRUD with few lines of code.
 *
 * @package    	grocery CRUD 
 * @author     	John Skoumbourdis <scoumbourdisj@gmail.com>
 * @license     https://github.com/scoumbourdis/grocery-crud/blob/master/license-grocery-crud.txt
 * @link		http://www.grocerycrud.com/documentation
 *
 * @hacker      Ez (ezgoen@gmail.com) - email me for my real identity
 */

class Gc_with_defaultvalue extends grocery_CRUD{

	protected $field_default_values = array();
	protected $inline='';
	
	function __construct()
	{
		parent::__construct();
		
		//I need these BEFORE render - so we can add include files
		$this->_initialize_variables();
	}
	
	
	/**
	 * override get_layout
	 *
	 * Created by Ez (ezgoen@gmail.com) - email me for my real identity
	 *
	 * @explanation:
	 *				I've added functionality to add inline code - like 
	 *				<style type=\"text/css\" > Blah </style>
	 *				<script type=\"text/javascript\"> Blah </script>";
	 *				and so on
	 *
	 *				The gc only has this at the time of the call to render
	 *				so we have to handle it separately
	 *				This is the last function called by render()
	 *					 
	 */
	protected function get_layout()	{		
		//call the parent method
		$result = parent::get_layout();
		//add our stuff
		$result->inline=$this->inline;
		return $result;
	}
	
	/**	 * Helper Function
	 *
	 * Created by Ez (ezgoen@gmail.com) - email me for my real identity
	 *
	 */
	protected function get_field_default_values($field){
		return array_key_exists($field,$this->field_default_values) ? $this->field_default_values[$field] : null;
	}

	/**
	 * Use this function exactly the same way you would use field_type()
	 * see : http://www.grocerycrud.com/documentation/options_functions/field_type
	 *
	 * Created by Ez (ezgoen@gmail.com) - email me for my real identity
	 *
	 * @explanation:
	 *				in the case of an ADD data form I want to provide default values to the fields.
	 *				I may also want to make the fields with default values readonly or completely hidden
	 *				The parent object does not allow/facilitate addidtion of default values to visible 
	 *				fields other than:
	 *					hidden
	 *					invisible
	 *					password
	 *					enum
	 *					set
	 *					dropdown
	 *					multiselect
	 *
	 *				I havent fooled around with invisible - so I cant comment on anything to do with that.
	 *				however I want my other fields - most commonly string and integer to have default values
	 *				additionally I may also want thos values to either editable OR readonly and visible at 
	 *				the same time AND I want my default values (if they are readonly) to go into the database
	 *				
	 */
	public function field_set_defaults($field,$type,$value){
		  $this->field_default_values[$field]=array('type'=>$type,'value'=>$value);
	}

	
	//for backward compatibility
	public function set_css($css_file)	{
		$this->add_include($css_file);
	}

	//for backward compatibility
	public function set_js($js_file){
		$this->add_include($js_file);
	}
	/**
	 * Use this function exactly the same way you would use set_js() or set_css
	 *
	 * Created by Ez (ezgoen@gmail.com) - email me for my real identity
	 *
	 * @explanation:
	 *				I wanted my controller to be able to do this too
	 *				I also use a menu library that I kinda adapted 
	 *				it uses some jquery as well - so its better everything uses the same function
	 *				to add js and script files so we dont :
	 *					end up with duplicates includes
	 *					degrade page load times
	 *				
	 *				This function also allows us to add files that are outside the grocerycrud 
	 *				dircetory tree
	 */
	public function add_include($inc_file){
		$fn = (strpos($inc_file,"/") === 0 ) ?  substr($inc_file, 1)  : $inc_file;
		if (strripos($fn,'.js',-3) !== False)  {  
			if (! file_exists("$fn")){
				$fn = $this->default_javascript_path."/$fn" ;
			}
			if(file_exists("$fn")){
				$this->js_files[sha1($fn)] = base_url().$fn;
			} else {
				throw new Exception("Missing JavaScript (.js) file. Tried : $inc_file and $fn");
			}
		} elseif (strripos($inc_file,'.css',-4) !== False) { 
			if (! file_exists("$fn")){
				$fn = $this->default_css_path."/$fn" ;
			}
			if(file_exists("$fn")){
				$this->css_files[sha1($fn)] = base_url().$fn;
			} else {
				throw new Exception("Missing Stylesheet (.css) file. Tried: $inc_file and $fn");
			}
		} else {
			throw new Exception("Request to include unknown file type: $inc_file");
		}
	}

	
	public function add_inline($str){
		$this->inline.="\n$str";
	}
	
	
	/**
	 * Work out what to do with input fields for add record
	 * 
	 *
	 * Modified/Hacked by Ez (ezgoen@gmail.com) - email me for my real identity
	 *
	 * @explanation:
	 *				in the case of an ADD data form I want to provide default values to the fields.
	 *				I may also want to make the fields with default values readonly or completely hidden
	 */
	protected function get_add_input_fields($field_values = null)
	{
		$fields = $this->get_add_fields();
		$types 	= $this->get_field_types();
		
		$input_fields = array();
		
		foreach($fields as $field_num => $field)
		{	
			$field_info = $types[$field->field_name];
			
			$field_value = !empty($field_values) && isset($field_values->{$field->field_name}) ? $field_values->{$field->field_name} : null;
	

			$field_default_values=$this->get_field_default_values($field->field_name);
			if (! empty($field_default_values) and empty($field_value) ){
				//if $field_info is not set at this point we need to construct one
				if (!isset($field_info)){
						$field_info = (object)array();
				}
				$field_info->custom_default=True; //this is what we look for to ensure we only change the behaviour we want to
				$field_info->crud_type=$field_default_values['type'];
				$field_info->type=$field_default_values['type'];
				$field_info->default=$field_default_values['value'];
				$field_info->extras=$field_default_values['value'];
				$field_value=$field_default_values['value'];
			}
			
			//no changes below here
			if(!isset($this->callback_add_field[$field->field_name]))
			{
				$field_input = $this->get_field_input($field_info, $field_value);
			}
			else
			{
				$field_input = $field_info;
				$field_input->input = call_user_func($this->callback_add_field[$field->field_name], $field_value, null, $field_info);
				//make our little change
				//allow default field rendering behaviour if user returns false
				//in the callback
				if ($field_input->input === False) {
					$field_input = $this->get_field_input($field_info, $field_value);
				}
			}
			
			switch ($field_info->crud_type) {
				case 'invisible':
					unset($this->add_fields[$field_num]);
					unset($fields[$field_num]);
					continue;
				break;
				case 'hidden':
					$this->add_hidden_fields[] = $field_input;
					unset($this->add_fields[$field_num]);
					unset($fields[$field_num]);
					continue;
				break;
			}			
			
			$input_fields[$field->field_name] = $field_input; 
		}
		
		return $input_fields;
	}
	
	/**
	 * Get the html input for the specific field with the 
	 * current value
	 * 
	 * @param object $field_info
	 * @param string $value
	 * @return object
	 *
	 * Modified/Hacked by Ez (ezgoen@gmail.com) - email me for my real identity
	 *
	 * @explanation:
	 *				In the case of an ADD data form I want my readonly default values to go into
	 *				the database.
	 *				The default behaviour of 'readonly' fields is to not actually "be a form field"
	 *				they are instead just text in a div like this :
	 *					<div id="field-YOUR_FIELD" class="readonly_label">Your Default Value</div>
	 *				this modification adds a hidden form field that will put the default values you 
	 *				want into the database like this :
	 *					<div id="field-YOUR_FIELD" class="readonly_label">Your Default Value</div>
	 *					<input id='field-YOUR_FIELD' type='hidden' name='YOUR_FIELD' value='Your Default Value' />
	 *
	 * Dynamic Where Clause MOD 
	 * @explanation:
	 *	 			To Facilitate a call to set_relation like: 
	 *
	 *				$crud->set_relation('OUR_ID','Other_Table','Other_Table_Field1',
	 *									array('Other_Table_Field2'=>'{This_Table_Some_Field_Value}'));
	 *																 ^^ notice the curly braces  ^^
	 *
	 *				Changed the argument lists to include current row data
	 *				that we then pass on to get_relation_input()
	 */
	protected function get_field_input($field_info, $value = null,$row_values = null)
	{
			$real_type = $field_info->crud_type;
			
			$types_array = array(
					'integer', 
					'text',
					'true_false',
					'string', 
					'date',
					'datetime',
					'enum',
					'set',
					'relation', 
					'relation_n_n',
					'upload_file',
					'hidden',
					'password', 
					'readonly',
					'dropdown',
					'multiselect'
			);
			if (in_array($real_type,$types_array)) {
				/* A quick way to go to an internal method of type $this->get_{type}_input . 
				 * For example if the real type is integer then we will use the method
				 * $this->get_integer_input
				 *  */
				 
				//Dynamic Where Clause : added $row_values  to the call to get_relation_input()
				//here I'm just handling the relation type - could do more types later
				//not sure why I'd want to tho
				if (in_array($real_type,array('relation'))){
					$field_info->input = $this->{"get_".$real_type."_input"}($field_info, $value, $row_values ); 
				} else {
					$field_info->input = $this->{"get_".$real_type."_input"}($field_info,$value ); 
				}
				//Default Value mod here...
				if ((property_exists($field_info,'custom_default')) and ( $real_type=="readonly" )) {
					$field_info->crud_type='hidden';
					$field_info->type='hidden';
					$field_info->input .= $this->{"get_hidden_input"}($field_info,$value); 
					$field_info->crud_type=$real_type;
					$field_info->type=$real_type;
				}
				//no changes below here
			}
			else
			{
				$field_info->input = $this->get_string_input($field_info,$value);
			}
		
		return $field_info;
	}


	/**
	 * get_edit_input_fields()
	 * 
	 * @param object $field_values
	 * @return object
	 *
	 * Modified/Hacked by Ez (ezgoen@gmail.com) - email me for my real identity
	 *
	 * @explanation:
	 *				extend the functionality - so that a person can do processing
	 *				using the current row data - but still alow default rendering
	 *				by returning boolean False instead of a string.
	 *
	 * Dynamic Where Clause MOD 
	 * @explanation:
	 *	 			To Facilitate a call to set_relation like: 
	 *
	 *				$crud->set_relation('OUR_ID','Other_Table','Other_Table_Field1',
	 *									array('Other_Table_Field2'=>'{This_Table_Some_Field_Value}'));
	 *																 ^^ notice the curly braces  ^^
	 *
	 *				Changed the argument lists for get_field_input() to include current row data
	 *
	 *
	 */
	protected function get_edit_input_fields($field_values = null)
	{
		$fields = $this->get_edit_fields();
		$types 	= $this->get_field_types();
		
		$input_fields = array();
		
		foreach($fields as $field_num => $field)
		{
			$field_info = $types[$field->field_name];			
			
			$field_value = !empty($field_values) && isset($field_values->{$field->field_name}) ? $field_values->{$field->field_name} : null;
			if(!isset($this->callback_edit_field[$field->field_name]))
			{			
			    // Dynamic Where Clause MOD - ADDDED $field_values to the call to get_field_input()
				$field_input = $this->get_field_input($field_info, $field_value, $field_values );
			}
			else
			{
				$primary_key = $this->getStateInfo()->primary_key;
				$field_input = $field_info;
				$field_input->input = call_user_func($this->callback_edit_field[$field->field_name], $field_value, $primary_key, $field_info, $field_values);
				//Default Values Mod
				//allow default field rendering behaviour if user returns false
				//in the callback
				if ($field_input->input === False) {
					// Dynamic Where Clause MOD - ADDDED $field_values to the call to get_field_input()
					$field_input = $this->get_field_input($field_info, $field_value, $field_values);
				}
			}
			
			switch ($field_info->crud_type) {
				case 'invisible':
					unset($this->edit_fields[$field_num]);
					unset($fields[$field_num]);
					continue;
				break;
				case 'hidden':
					$this->edit_hidden_fields[] = $field_input;
					unset($this->edit_fields[$field_num]);
					unset($fields[$field_num]);
					continue;
				break;				
			}			
			
			$input_fields[$field->field_name] = $field_input; 
		}
		
		return $input_fields;
	}	
	
	
	
	/**
	 * override get_relation_input
	 *
	 * Created by Ez (ezgoen@gmail.com) - email me for my real identity
	 *
	 * @explanation:
	 *				Dynamic Where clause Mod
	 *				This is where the magic (well, not really) happens 
	 *				we alter the where clause just before 
	 *				the call to th eparent get_relation_input()
	 *					 
	 */
	
	protected function get_relation_input($field_info, $value, $row_values){
		
		list($_field_name , $_related_table, $_related_title_field, $_where_clause , $_order_by ) = $field_info->extras;
		
		if (!empty($row_values) and !empty($_where_clause)){
			$wc=(array) $_where_clause;
			foreach ($wc as $where_key => $where_value){
				foreach ($row_values as $key => $value){
					if ($where_value=="{".$key."}") {
						$_where_clause[$where_key]=$value;
					}					
				}
			}
		}
		$field_info->extras= array($_field_name , $_related_table, $_related_title_field, $_where_clause , $_order_by ) ;
		
		//for some reason that still eludes me $value comes in as a different field value when there is a where clause
		//completely bewildered and no time to dig any deeper - is it a bug ????
		if ( !empty($row_values) ) {
			//this forces $value to be what its supposed to be
			$value = $row_values->$_field_name ;
		}
		
		return parent::get_relation_input($field_info,$value);
	}

	
	/**
	 * change_list_value
	 *
	 * Patched by Ez (ezgoen@gmail.com) - email me for my real identity
	 *
	 * @explanation:
	 *				There was a bug where images with uppercase file extensions
	 *				were'nt being displayed inline.
	 *
	 *				This, along with a change to get_upload_file_input
	 *				and /assets/grocery_crud/js/jquery_plugins/config/jquery.fileupload.config.js
	 *
	 *				fixes the behaviour					 
	 */
	protected function change_list_value($field_info, $value = null)	{
		$real_type = $field_info->crud_type;
		
		switch ($real_type) {
			case 'hidden':
			case 'invisible':
			case 'integer':
				
			break;
			case 'true_false':
				if(isset($this->default_true_false_text[$value]))
					$value = $this->default_true_false_text[$value];
			break;
			case 'string':
				$value = $this->character_limiter($value,$this->character_limiter,"...");
			break;
			case 'text':
				$value = $this->character_limiter(strip_tags($value),$this->character_limiter,"...");
			break;
			case 'date':
				if(!empty($value) && $value != '0000-00-00' && $value != '1970-01-01')
				{
					list($year,$month,$day) = explode("-",$value);
					
					$value = date($this->php_date_format, mktime (0, 0, 0, (int)$month , (int)$day , (int)$year));
				}
				else 
				{
					$value = '';
				}
			break;
			case 'datetime':
				if(!empty($value) && $value != '0000-00-00 00:00:00' && $value != '1970-01-01 00:00:00')
				{
					list($year,$month,$day) = explode("-",$value);
					list($hours,$minutes) = explode(":",substr($value,11));
					
					$value = date($this->php_date_format." - H:i", mktime ((int)$hours , (int)$minutes , 0, (int)$month , (int)$day ,(int)$year));
				}
				else 
				{
					$value = '';
				}
			break;
			case 'enum':
				$value = $this->character_limiter($value,$this->character_limiter,"...");
			break;

			case 'multiselect':
				$value_as_array = array();
				foreach(explode(",",$value) as $row_value)
				{
					$value_as_array[] = array_key_exists($row_value,$field_info->extras) ? $field_info->extras[$row_value] : $row_value;
				}
				$value = implode(",",$value_as_array);
			break;			
			
			case 'relation_n_n':
				$value = $this->character_limiter(str_replace(',',', ',$value),$this->character_limiter,"...");
			break;						
			
			case 'password':
				$value = '******';
			break;
			
			case 'dropdown':
				$value = array_key_exists($value,$field_info->extras) ? $field_info->extras[$value] : $value; 
			break;			
			
			case 'upload_file':
				if(empty($value))
				{
					$value = "";
				}
				else
				{
					//uppercase file extension fix :
					$is_image = !empty($value) &&
					(strripos($value,'.jpg',-4) !== False
							|| strripos($value,'.png',-4) !== False
							|| strripos($value,'.jpeg',-5) !== False
							|| strripos($value,'.gif',-4) !== False
							|| strripos($value,'.tiff',-5) !== False)
							? true : false;
					//: uppercase file extension fix
								
					$file_url = base_url().$field_info->extras->upload_path."/$value";
					
					$file_url_anchor = '<a href="'.$file_url.'"';
					if($is_image)
					{
						$file_url_anchor .= ' class="image-thumbnail"><img src="'.$file_url.'" height="50px">';
					}
					else
					{
						$file_url_anchor .= ' target="_blank">'.$this->character_limiter($value,$this->character_limiter,'...',true);
					}
					$file_url_anchor .= '</a>';
					
					$value = $file_url_anchor;
				}
			break;
			
			default:
				$value = $this->character_limiter($value,$this->character_limiter,"...");
			break;
		}
		
		return $value;
	}
	
	
	/**
	 * get_upload_file_input
	 *
	 * Patched by Ez (ezgoen@gmail.com) - email me for my real identity
	 *
	 * @explanation:
	 *				There was a bug where images with uppercase file extensions
	 *				were'nt being displayed inline.
	 *
	 *				This, along with a change to change_list_value
	 *				and /assets/grocery_crud/js/jquery_plugins/config/jquery.fileupload.config.js
	 *
	 *				fixes the behaviour					 
	 */
	
	protected function get_upload_file_input($field_info, $value){
		$this->set_css($this->default_css_path.'/ui/simple/'.grocery_CRUD::JQUERY_UI_CSS);
		$this->set_css($this->default_css_path.'/jquery_plugins/file_upload/file-uploader.css');
		$this->set_css($this->default_css_path.'/jquery_plugins/file_upload/jquery.fileupload-ui.css');

		$this->set_js($this->default_javascript_path.'/jquery_plugins/ui/'.grocery_CRUD::JQUERY_UI_JS);
		$this->set_js($this->default_javascript_path.'/jquery_plugins/tmpl.min.js');
		$this->set_js($this->default_javascript_path.'/jquery_plugins/load-image.min.js');

		$this->set_js($this->default_javascript_path.'/jquery_plugins/jquery.iframe-transport.js');
		$this->set_js($this->default_javascript_path.'/jquery_plugins/jquery.fileupload.js');
		$this->set_js($this->default_javascript_path.'/jquery_plugins/config/jquery.fileupload.config.js');
		
		//Fancybox
		$this->set_css($this->default_css_path.'/jquery_plugins/fancybox/jquery.fancybox.css');
		
		$this->set_js($this->default_javascript_path.'/jquery_plugins/jquery.fancybox.pack.js');
		$this->set_js($this->default_javascript_path.'/jquery_plugins/jquery.easing-1.3.pack.js');	
		$this->set_js($this->default_javascript_path.'/jquery_plugins/config/jquery.fancybox.config.js');		
		
		$unique = uniqid();
		
		$allowed_files = $this->config->file_upload_allow_file_types;
		$allowed_files_ui = '.'.str_replace('|',',.',$allowed_files);
		$max_file_size_ui = $this->config->file_upload_max_file_size;
		$max_file_size_bytes = $this->_convert_bytes_ui_to_bytes($max_file_size_ui);
		
		$this->_inline_js('
			var upload_info_'.$unique.' = { 
				accepted_file_types: /(\\.|\\/)('.$allowed_files.')$/i, 
				accepted_file_types_ui : "'.$allowed_files_ui.'", 
				max_file_size: '.$max_file_size_bytes.', 
				max_file_size_ui: "'.$max_file_size_ui.'" 
			};
				
			var string_upload_file 	= "'.$this->l('form_upload_a_file').'";
			var string_delete_file 	= "'.$this->l('string_delete_file').'";
			var string_progress 			= "'.$this->l('string_progress').'";
			var error_on_uploading 			= "'.$this->l('error_on_uploading').'";
			var message_prompt_delete_file 	= "'.$this->l('message_prompt_delete_file').'";
			
			var error_max_number_of_files 	= "'.$this->l('error_max_number_of_files').'";
			var error_accept_file_types 	= "'.$this->l('error_accept_file_types').'";
			var error_max_file_size 		= "'.str_replace("{max_file_size}",$max_file_size_ui,$this->l('error_max_file_size')).'";
			var error_min_file_size 		= "'.$this->l('error_min_file_size').'";

			var base_url = "'.base_url().'";
			var upload_a_file_string = "'.$this->l('form_upload_a_file').'";			
		');

		$uploader_display_none 	= empty($value) ? "" : "display:none;";
		$file_display_none  	= empty($value) ?  "display:none;" : "";
		
		//uppercase file extension fix :
		$is_image = !empty($value) &&
					(strripos($value,'.jpg',-4) !== False
							|| strripos($value,'.png',-4) !== False
							|| strripos($value,'.jpeg',-5) !== False
							|| strripos($value,'.gif',-4) !== False
							|| strripos($value,'.tiff',-5) !== False)
							? true : false;
		//: uppercase file extension fix
		$image_class = $is_image ? 'image-thumbnail' : '';
		
		$input = '<span class="fileinput-button qq-upload-button" id="upload-button-'.$unique.'" style="'.$uploader_display_none.'">
			<span>'.$this->l('form_upload_a_file').'</span>
			<input type="file" name="'.$this->_unique_field_name($field_info->name).'" class="gc-file-upload" rel="'.$this->getUploadUrl($field_info->name).'" id="'.$unique.'">
			<input class="hidden-upload-input" type="hidden" name="'.$field_info->name.'" value="'.$value.'" rel="'.$this->_unique_field_name($field_info->name).'" />
		</span>';
		
		$this->set_css($this->default_css_path.'/jquery_plugins/file_upload/fileuploader.css');
		
		$file_url = base_url().$field_info->extras->upload_path.'/'.$value;
		
		$input .= "<div id='uploader_$unique' rel='$unique' class='grocery-crud-uploader' style='$uploader_display_none'></div>";
		$input .= "<div id='success_$unique' class='upload-success-url' style='$file_display_none padding-top:7px;'>";
		$input .= "<a href='".$file_url."' id='file_$unique' class='open-file";
		$input .= $is_image ? " $image_class'><img src='".$file_url."' height='50px'>" : "' target='_blank'>$value";
		$input .= "</a> ";
		$input .= "<a href='javascript:void(0)' id='delete_$unique' class='delete-anchor'>".$this->l('form_upload_delete')."</a> ";
		$input .= "</div><div style='clear:both'></div>";
		$input .= "<div id='loading-$unique' style='display:none'><span id='upload-state-message-$unique'></span> <span class='qq-upload-spinner'></span> <span id='progress-$unique'></span></div>";
		$input .= "<div style='display:none'><a href='".$this->getUploadUrl($field_info->name)."' id='url_$unique'></a></div>";
		$input .= "<div style='display:none'><a href='".$this->getFileDeleteUrl($field_info->name)."' id='delete_url_$unique' rel='$value' ></a></div>";
		
		return $input;
	}
}
?>
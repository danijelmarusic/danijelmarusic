<?php


$page_security = 3;
$path_to_root="../..";
include($path_to_root . "/includes/session.inc");

page(_("Sales Groups"));

include($path_to_root . "/includes/ui.inc");

simple_page_mode(true);

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') 
{

	$input_error = 0;

	if (strlen($_POST['description']) == 0) 
	{
		$input_error = 1;
		display_error(_("The area description cannot be empty."));
		set_focus('description');
	}

	if ($input_error != 1)
	{
    	if ($selected_id != -1) 
    	{
    		$sql = "UPDATE ".TB_PREF."groups SET description=".db_escape($_POST['description'])." WHERE id = '$selected_id'";
			$note = _('Selected sales group has been updated');
    	} 
    	else 
    	{
    		$sql = "INSERT INTO ".TB_PREF."groups (description) VALUES (".db_escape($_POST['description']) . ")";
			$note = _('New sales group has been added');
    	}
    
    	db_query($sql,"The sales group could not be updated or added");
		display_notification($note);    	
		$Mode = 'RESET';
	}
} 

if ($Mode == 'Delete')
{

	$cancel_delete = 0;

	// PREVENT DELETES IF DEPENDENT RECORDS IN 'debtors_master'

	$sql= "SELECT COUNT(*) FROM ".TB_PREF."debtors_master WHERE group_no='$selected_id'";
	$result = db_query($sql,"check failed");
	$myrow = db_fetch_row($result);
	if ($myrow[0] > 0) 
	{
		$cancel_delete = 1;
		display_error(_("Cannot delete this group because customers have been created using this group."));
	} 
	if ($cancel_delete == 0) 
	{
		$sql="DELETE FROM ".TB_PREF."groups WHERE id='" . $selected_id . "'";
		db_query($sql,"could not delete sales group");

		display_notification(_('Selected sales group has been deleted'));
	} //end if Delete area
	$Mode = 'RESET';
} 

if ($Mode == 'RESET')
{
	$selected_id = -1;
	unset($_POST);
}
//-------------------------------------------------------------------------------------------------

$sql = "SELECT * FROM ".TB_PREF."groups ORDER BY description";
$result = db_query($sql,"could not get groups");

start_form();
start_table("$table_style width=40%");
$th = array(_("Group Name"), "", "");
table_header($th);
$k = 0; 

while ($myrow = db_fetch($result)) 
{
	
	alt_table_row_color($k);
		
	label_cell($myrow["description"]);
 	edit_button_cell("Edit".$myrow["id"], _("Edit"));
 	edit_button_cell("Delete".$myrow["id"], _("Delete"));
	end_row();
}


end_table();
end_form();
echo '<br>';

//-------------------------------------------------------------------------------------------------

start_form();

start_table("$table_style2 width=40%");

if ($selected_id != -1) 
{
 	if ($Mode == 'Edit') {
		//editing an existing area
		$sql = "SELECT * FROM ".TB_PREF."groups WHERE id='$selected_id'";

		$result = db_query($sql,"could not get group");
		$myrow = db_fetch($result);

		$_POST['description']  = $myrow["description"];
	}
	hidden("selected_id", $selected_id);
} 

text_row_ex(_("Group Name:"), 'description', 30); 

end_table(1);

submit_add_or_update_center($selected_id == -1, '', true);

end_form();

end_page();
?>

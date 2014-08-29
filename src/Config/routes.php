<?php
//--------------------------------------------------------------//
//              PROJECT
//--------------------------------------------------------------//
$routes[] = array(
	"route" 		=> '/',
	'type' 			=> 'get',
	"controller" 	=> 'Frontend',
	"method" 		=> "index",
);

$routes[] = array(
	"route" 		=> '/new/',
	'type' 			=> 'get',
	"controller" 	=> 'Frontend',
	"method" 		=> "newProject",
);

$routes[] = array(
	"route" 		=> '/new/',
	'type' 			=> 'post',
	"controller" 	=> 'Frontend',
	"method" 		=> "saveProject",
);

/*
$routes[] = array(
	"route" 		=> '/new/success',
	'type' 			=> 'post',
	"controller" 	=> 'Frontend',
	"method" 		=> 'saveProject',
	"bind"			=> 'project.definition.index'
);
*/

$routes[] = array(
	"route" 		=> '/project/{id}/definition',
	'type' 			=> 'get',
	"controller" 	=> 'Contributor',
	"method" 		=> 'definition',
	"bind"			=> 'project.definition.index'
);

$routes[] = array(
	"route" 		=> '/project/{id_project}/json',
	'type' 			=> 'get',
	"controller" 	=> 'Contributor',
	"method" 		=> 'getJSON',
	"bind"			=> 'project.data.json'
);
//--------------------------------------------------------------//


//--------------------------------------------------------------//
//              DONNEES
//--------------------------------------------------------------//
$routes[] = array(
	"route" 		=> '/project/{id_project}/data',
	'type' 			=> 'get',
	"controller" 	=> 'Contributor',
	"method" 		=> 'data',
	"bind"			=> 'project.data.index'
);
$routes[] = array(
	"route" 		=> '/project/{id_project}/data/{id_field}',
	'type' 			=> 'get',
	"controller" 	=> 'FieldController',
	"method" 		=> 'editData',
	"bind"			=> 'project.data.field.edit'
);

$routes[] = array(
	"route" 		=> '/project/{id_project}/data/{id_field}',
	'type' 			=> 'post',
	"controller" 	=> 'FieldController',
	"method" 		=> 'saveData'
);

$routes[] = array(
	"route" 		=> '/project/{id_project}/data/{id_field}/index/{index}/edit',
	'type' 			=> 'get',
	"controller" 	=> 'ObjectFieldController',
	"method" 		=> 'editData',
	"bind"			=> 'object.field.data.edit'
);

$routes[] = array(
	"route" 		=> '/project/{id_project}/data/{id_field}/add',
	'type' 			=> 'get',
	"controller" 	=> 'ObjectFieldController',
	"method" 		=> 'addData',
	"bind"			=> 'object.field.data.new'
);


$routes[] = array(
	"route" 		=> '/project/{id_project}/data/{id_field}/add',
	'type' 			=> 'post',
	"controller" 	=> 'ObjectFieldController',
	"method" 		=> 'createData'
);

$routes[] = array(
	"route" 		=> '/project/{id_project}/data/{id_field}/index/{index}/delete',
	'type' 			=> 'get',
	"controller" 	=> 'ObjectFieldController',
	"method" 		=> 'removeData',
	"bind"			=> 'object.field.data.delete'
);

$routes[] = array(
	"route" 		=> '/project/{id_project}/data/{id_field}/index/{index}/edit',
	'type' 			=> 'post',
	"controller" 	=> 'ObjectFieldController',
	"method" 		=> 'saveData'
);
//--------------------------------------------------------------//


//--------------------------------------------------------------//
//              FIELDS
//--------------------------------------------------------------//

$routes[] = array(
	"route" 		=> '/project/{id_project}/definition/field/new',
	'type' 			=> 'get',
	"controller" 	=> 'FieldController',
	"method" 		=> 'newField',
	"bind"			=> 'project.field.new'
);

$routes[] = array(
	"route" 		=> '/project/{id_project}/definition/field/new',
	'type' 			=> 'post',
	"controller" 	=> 'FieldController',
	"method" 		=> 'save',
	"bind"			=> 'project.field.create'
);

$routes[] = array(
	"route" 		=> '/project/{id_project}/definition/field/{id_field}',
	'type' 			=> 'get',
	"controller" 	=> 'FieldController',
	"method" 		=> 'edit',
	"bind"			=> 'project.field.edit'
);

$routes[] = array(
	"route" 		=> '/project/{id_project}/definition/field/{id_field}',
	'type' 			=> 'post',
	"controller" 	=> 'FieldController',
	"method" 		=> 'save'
);

//--------------------------------------------------------------//



//--------------------------------------------------------------//
//              OBJECT
//--------------------------------------------------------------//
$routes[] = array(
	"route" 		=> '/project/{id_project}/definition/object/new',
	'type' 			=> 'get',
	"controller" 	=> 'ObjectController',
	"method" 		=> 'newObject',
	"bind"			=> 'project.object.new'
);

$routes[] = array(
	"route" 		=> '/project/{id_project}/definition/object/new',
	'type' 			=> 'post',
	"controller" 	=> 'ObjectController',
	"method" 		=> 'save',
	"bind"			=> 'project.object.create'
);

$routes[] = array(
	"route" 		=> '/project/{id_project}/definition/object/{id_object}',
	'type' 			=> 'get',
	"controller" 	=> 'ObjectController',
	"method" 		=> 'edit',
	"bind"			=> 'project.object.edit'
);
//--------------------------------------------------------------//


//--------------------------------------------------------------//
//              OBJECT FIELD
//--------------------------------------------------------------//
$routes[] = array(
	"route" 		=> '/project/{id_project}/definition/object/{id_object}/new',
	'type' 			=> 'get',
	"controller" 	=> 'ObjectFieldController',
	"method" 		=> 'newField',
	"bind"			=> 'object.field.new'
);

$routes[] = array(
	"route" 		=> '/project/{id_project}/definition/object/{id_object}/new',
	'type' 			=> 'post',
	"controller" 	=> 'ObjectFieldController',
	"method" 		=> 'save'
);

$routes[] = array(
	"route" 		=> '/project/{id_project}/definition/object/{id_object}/field/{id_field}',
	'type' 			=> 'get',
	"controller" 	=> 'ObjectFieldController',
	"method" 		=> 'edit',
	"bind"			=> 'object.field.edit'
);

$routes[] = array(
	"route" 		=> '/project/{id_project}/definition/object/{id_object}/field/{id_field}',
	'type' 			=> 'get',
	"controller" 	=> 'ObjectFieldController',
	"method" 		=> 'edit',
	"bind"			=> 'object.field.edit'
);


//--------------------------------------------------------------//
?>
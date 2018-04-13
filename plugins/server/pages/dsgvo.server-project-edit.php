<?php
echo rex_view::title($this->i18n('dsgvo'));

	$func = rex_request('func', 'string');
	
	if ($func == '') {
		$list = rex_list::factory("SELECT * FROM `".rex::getTablePrefix()."dsgvo_server_project` ORDER BY `domain` ASC");
		$list->addTableAttribute('class', 'table-striped');
		$list->setNoRowsMessage($this->i18n('sets_norowsmessage'));
		
		// icon column
		$thIcon = '<a href="'.$list->getUrl(['func' => 'add']).'"><i class="rex-icon rex-icon-add-action"></i></a>';
		$tdIcon = '<i class="rex-icon fa-file-text-o"></i>';
		$list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
		$list->setColumnParams($thIcon, ['func' => 'edit', 'id' => '###id###']);
		
		$list->setColumnLabel('domain', $this->i18n('dsgvo_server_projects_column_domain'));
		$list->setColumnParams('domain', ['id' => '###id###', 'func' => 'edit']);
		
		$list->removeColumn('id');

		$th = $this->i18n('dsgvo_server_projects_column_last_call');
		$td = '<i class="rex-icon rex-icon-success"></i>';
		$list->addColumn($th, $td, 5, ['<th class="">###VALUE###</th>', '<td class="">###VALUE###</td>']);
		
		$th = $this->i18n('dsgvo_server_projects_column_manage_text');
		$td = '<i class="rex-icon rex-icon-success"></i>';
		$list->addColumn($th, $td, 5, ['<th class="">###VALUE###</th>', '<td class="">###VALUE###</td>']);
		
		$content = $list->get();
		
		$fragment = new rex_fragment();
		$fragment->setVar('class', "info", false);
		$fragment->setVar('title', $this->i18n('dsgvo_client_project_server_title'), false);
		$fragment->setVar('content', $content, false);
		$content = $fragment->parse('core/page/section.php');
		
		echo $content;
	} else if ($func == 'add' || $func == 'edit') {
		$id = rex_request('id', 'int');
		
		if ($func == 'edit') {
			$formLabel = $this->i18n('dsgvo_server_text_edit');
		} elseif ($func == 'add') {
			$formLabel = $this->i18n('dsgvo_server_text_add');
		}
		
		$form = rex_form::factory(rex::getTablePrefix().'dsgvo_server_project', '', 'id='.$id);
		
		//Start - add name-field
			$field = $form->addTextField('domain');
			$field->setLabel($this->i18n('dsgvo_server_projects_column_domain'));
			$field->setNotice($this->i18n('sets_label_domain_note'));
		//End - add name-field
		
		if ($func == 'edit') {
			$form->addParam('id', $id);
		}

		$content = $form->get();

		
		$fragment = new rex_fragment();
		$fragment->setVar('class', 'edit', false);
		$fragment->setVar('title', $formLabel, false);
		$fragment->setVar('body', $content, false);
		$content = $fragment->parse('core/page/section.php');
		
		$content .= "Hier Logs hinzufügen";

		echo $content;
	}
?>
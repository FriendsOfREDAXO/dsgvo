<?php
echo rex_view::title($this->i18n('dsgvo'));

	$func = rex_request('func', 'string');
	
	if ($func == '') {
		$list = rex_list::factory("SELECT * FROM `".rex::getTablePrefix()."yf_dsgvo` ORDER BY `prio` ASC");
		$list->addTableAttribute('class', 'table-striped');
		$list->setNoRowsMessage($this->i18n('sets_norowsmessage'));
		
		// icon column
		$thIcon = '<a href="'.$list->getUrl(['func' => 'add']).'"><i class="rex-icon rex-icon-add-action"></i></a>';
		$tdIcon = '<i class="rex-icon fa-file-text-o"></i>';
		$list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
		$list->setColumnParams($thIcon, ['func' => 'edit', 'id' => '###id###']);
		
		$list->setColumnLabel('name', $this->i18n('sets_column_name'));
		$list->setColumnLabel('type', $this->i18n('sets_column_type'));		
		$list->setColumnParams('name', ['id' => '###id###', 'func' => 'edit']);

		$list->setColumnLabel('name', $this->i18n('dsgvo_server_text_column_name'));
		$list->setColumnLabel('source', $this->i18n('dsgvo_server_text_column_source'));

		$list->setColumnLabel('status', $this->i18n('dsgvo_server_text_column_status'));
		
		$list->removeColumn('id');
		$list->removeColumn('text');
		$list->removeColumn('custom_text');
		$list->removeColumn('source_url');
		
		$content = $list->get();
		
		$fragment = new rex_fragment();
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
		
		$form = rex_form::factory(rex::getTablePrefix().'yf_dsgvo', '', 'id='.$id);

		//Start - add keyword-field
			$field = $form->addTextField('keyword');
			$field->setLabel($this->i18n('dsgvo_server_text_column_keyword'));
			$field = $form->addRawField('<dl class="rex-form-group form-group"><dt>&nbsp;</dt><dd><p class="help-block rex-note">'.$this->i18n('sets_label_keyword_note').'</p></dd></dl>');
		//End - add keyword-field
		
		//Start - add name-field
			$field = $form->addTextField('name');
			$field->setLabel($this->i18n('dsgvo_server_text_column_name'));
			$field = $form->addRawField('<dl class="rex-form-group form-group"><dt>&nbsp;</dt><dd><p class="help-block rex-note">'.$this->i18n('sets_label_name_note').'</p></dd></dl>');
		//End - add name-field
		
		//Start - add status-field 
			$field = $form->addSelectField('status');
		    $field->setLabel($this->i18n('dsgvo_server_text_column_status'));
		    $select = $field->getSelect();
		    $select->setSize(1);
		    $select->addOption($this->i18n('dsgvo_server_text_column_status_is_online'), 1);
		    $select->addOption($this->i18n('dsgvo_server_text_column_status_is_offline'), 0);
		    if ($func == 'add') {
		        $select->setSelected(1);
		    }		
		    $field = $form->addRawField('<dl class="rex-form-group form-group"><dt>&nbsp;</dt><dd><p class="help-block rex-note">'.$this->i18n('sets_label_status_note').'</p></dd></dl>');
		//End - add status-field
		
		//Start - add text-field
			$field = $form->addTextAreaField('text');
			$field->setLabel($this->i18n('dsgvo_server_text_column_text'));
			$field = $form->addRawField('<dl class="rex-form-group form-group"><dt>&nbsp;</dt><dd><p class="help-block rex-note">'.$this->i18n('sets_label_text_note').'</p></dd></dl>');
		//End - add text-field
		
		//Start - add source-field
			$field = $form->addTextField('source');
			$field->setLabel($this->i18n('dsgvo_server_text_column_source'));
			$field = $form->addRawField('<dl class="rex-form-group form-group"><dt>&nbsp;</dt><dd><p class="help-block rex-note">'.$this->i18n('sets_label_source_note').'</p></dd></dl>');
		//End - add source-field
		
		//Start - add source_url-field
			$field = $form->addTextField('source_url');
			$field->setLabel($this->i18n('dsgvo_server_text_column_source_url'));
			$field = $form->addRawField('<dl class="rex-form-group form-group"><dt>&nbsp;</dt><dd><p class="help-block rex-note">'.$this->i18n('sets_label_source_url_note').'</p></dd></dl>');
		//End - add source_url-field
		
		if ($func == 'edit') {
			$form->addParam('id', $id);
		}
		
		$content = $form->get();
		
		$fragment = new rex_fragment();
		$fragment->setVar('class', 'edit', false);
		$fragment->setVar('title', $formLabel, false);
		$fragment->setVar('body', $content, false);
		$content = $fragment->parse('core/page/section.php');
		
		echo $content;
	}
?>
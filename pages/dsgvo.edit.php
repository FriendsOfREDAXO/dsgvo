<?php
echo rex_view::title($this->i18n('dsgvo'));

	$func = rex_request('func', 'string');
	
	if ($func == '') {

		$list = rex_list::factory("SELECT * FROM `".rex::getTablePrefix()."dsgvo_client` ORDER BY `prio` ASC", 20);
		$list->addTableAttribute('class', 'table-striped');
		$list->setNoRowsMessage($this->i18n('sets_norowsmessage'));
		
		// icon column
		$thIcon = '<a href="'.$list->getUrl(['func' => 'add']).'"><i class="rex-icon rex-icon-add-action"></i></a>';
		$tdIcon = '<i class="rex-icon fa-edit"></i>';
		$list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
		$list->setColumnParams($thIcon, ['func' => 'edit', 'id' => '###id###']);
		
		$list->setColumnLabel('name', $this->i18n('dsgvo_client_text_column_name'));
		$list->setColumnLabel('source', $this->i18n('dsgvo_client_text_column_source'));

		$list->setColumnLabel('status', $this->i18n('dsgvo_client_text_column_status'));
		//$list->setColumnParams('status', ['func' => 'setstatus', 'oldstatus' => '###status###', 'oid' => '###id###']);
		//$list->setColumnLayout('status', ['<th class="rex-table-action" colspan="4">###VALUE###</th>', '<td class="rex-table-action">###VALUE###</td>']);
		$list->setColumnFormat('status', 'custom', function ($params) {
			$list = $params['list'];  
	        if ($params['value'] == "") {
	            $str = rex_i18n::msg('cronjob_status_invalid');
	        } elseif ($params['value'] == 1) {
	            $str = $list->getColumnLink('status', '<span class="rex-online"><i class="rex-icon rex-icon-active-true"></i> ' . rex_i18n::msg('dsgvo_client_text_column_status_is_online') . '</span>');
	        } else {
	            $str = $list->getColumnLink('status', '<span class="rex-offline"><i class="rex-icon rex-icon-active-false"></i> ' . rex_i18n::msg('dsgvo_client_text_column_status_is_offline') . '</span>');
	        }
	        return $str;
	    });
		$list->setColumnParams('name', ['id' => '###id###', 'func' => 'edit']);
		
		$list->removeColumn('id');
		$list->removeColumn('text');
		$list->removeColumn('custom_text');
		$list->removeColumn('source_url');
		
		$content = $list->get();
		
		 $fragment = new rex_fragment();
		 $fragment->setVar('class', "info", false);
		 $fragment->setVar('title', "Diese Texte werden in der Datenschutz-Seite ausgegeben.", false);
		 $fragment->setVar('content', $content, false);
		 $content = $fragment->parse('core/page/section.php');
		
		echo $content;
	} else if ($func == 'add' || $func == 'edit') {
		$id = rex_request('id', 'int');
		
		if ($func == 'edit') {
			$formLabel = $this->i18n('dsgvo_client_text_edit');
		} elseif ($func == 'add') {
			$formLabel = $this->i18n('dsgvo_client_text_add');
		}
		
		$form = rex_form::factory(rex::getTablePrefix().'dsgvo_client', '', 'id='.$id);

		//Start - add keyword-field
			$field = $form->addTextField('keyword');
			$field->setLabel($this->i18n('dsgvo_client_text_column_keyword'));
			$field = $form->addRawField('<dl class="rex-form-group form-group"><dt>&nbsp;</dt><dd><p class="help-block rex-note">'.$this->i18n('sets_label_keyword_note').'</p></dd></dl>');
		//End - add keyword-field

		//Start - add name-field
			$field = $form->addTextField('name');
			$field->setLabel($this->i18n('dsgvo_client_text_column_name'));
			$field = $form->addRawField('<dl class="rex-form-group form-group"><dt>&nbsp;</dt><dd><p class="help-block rex-note">'.$this->i18n('sets_label_name_note').'</p></dd></dl>');
		//End - add name-field
		
		//Start - add status-field 
			$field = $form->addSelectField('status');
		    $field->setLabel($this->i18n('dsgvo_client_text_column_status'));
		    $select = $field->getSelect();
		    $select->setSize(1);
		    $select->addOption($this->i18n('dsgvo_client_text_column_status_is_online'), 1);
		    $select->addOption($this->i18n('dsgvo_client_text_column_status_is_offline'), 0);
		    if ($func == 'add') {
		        $select->setSelected(1);
		    }		
		    $field = $form->addRawField('<dl class="rex-form-group form-group"><dt>&nbsp;</dt><dd><p class="help-block rex-note">'.$this->i18n('sets_label_status_note').'</p></dd></dl>');
		//End - add status-field

		//Start - add text-field
			$field = $form->addTextAreaField('text');
			$field->setLabel($this->i18n('dsgvo_client_text_column_text'));
			$field = $form->addRawField('<dl class="rex-form-group form-group"><dt>&nbsp;</dt><dd><p class="help-block rex-note">'.$this->i18n('sets_label_text_note').'</p></dd></dl>');
		//End - add text-field
		
		//Start - add custom_text-field
			$field = $form->addTextAreaField('custom_text');
			$field->setLabel($this->i18n('dsgvo_client_text_column_custom_text'));
			$field = $form->addRawField('<dl class="rex-form-group form-group"><dt>&nbsp;</dt><dd><p class="help-block rex-note">'.$this->i18n('sets_label_custom_text_note').'</p></dd></dl>');
		//End - add custom_text-field
		
		//Start - add source-field
			$field = $form->addTextField('source');
			$field->setLabel($this->i18n('dsgvo_client_text_column_source'));
			$field = $form->addRawField('<dl class="rex-form-group form-group"><dt>&nbsp;</dt><dd><p class="help-block rex-note">'.$this->i18n('sets_label_source_note').'</p></dd></dl>');
		//End - add source-field

		//Start - add source_url-field
			$field = $form->addTextField('source_url');
			$field->setLabel($this->i18n('dsgvo_client_text_column_source_url'));
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
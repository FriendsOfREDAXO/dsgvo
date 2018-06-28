<?php
echo rex_view::title($this->i18n('dsgvo'));

	$func = rex_request('func', 'string');
	$domain = rex_request('domain', 'string');
	$result = rex_request('result', 'string', false);

	if (($func == '' && !$result) || $func == "domain_delete") { 

		if($func == 'domain_delete') {
    		$oid = rex_request('oid', 'int');
			$delete = rex_sql::factory()->setQuery('DELETE FROM rex_dsgvo_server_project WHERE id = :oid',array(':oid' => $oid));
			$delete = rex_sql::factory()->setDebug(0)->setQuery('DELETE FROM rex_dsgvo_server WHERE domain = :domain',array(':domain' => $domain));
			echo rex_view::success( $this->i18n('dsgvo_server_domain_deleted'));
		}	

		$old_domain = rex_request("old_domain", "string", "");
		$new_domain = array_shift(rex_sql::factory()->getArray('SELECT domain FROM rex_dsgvo_server_project WHERE id = :id',array(':id' => rex_request('id', 'int'))))['domain'];

		if($old_domain && $old_domain != $new_domain) {
			$update = rex_sql::factory()->setQuery('UPDATE rex_dsgvo_server SET domain = :new_domain WHERE domain = :old_domain',array(':new_domain' => $new_domain, ':old_domain' => $old_domain));
			$update = rex_sql::factory()->setQuery('UPDATE rex_dsgvo_server_log SET domain = :new_domain WHERE domain = :old_domain',array(':new_domain' => $new_domain, ':old_domain' => $old_domain));
			echo rex_view::success( $this->i18n('dsgvo_server_domain_renamed'));
    	} else if ($old_domain) {
			echo rex_view::success( $this->i18n('dsgvo_server_domain_updated'));
		}

		// Domain-Übersicht ANFANG //
		$query = 'SELECT P.id, P.domain, api_key, count_text, count_total, has_code, logdate, last_change, description FROM `rex_dsgvo_server_project` AS P LEFT JOIN (SELECT COUNT(id) AS count_total, COUNT(IF(status=1,1,NULL)) AS count_text, COUNT(IF(code = "" OR code IS NULL,NULL,1)) AS has_code, domain, max(updatedate) AS last_change FROM rex_dsgvo_server GROUP BY domain) as S ON P.domain = S.domain LEFT JOIN (SELECT max(createdate) as logdate, domain FROM rex_dsgvo_server_log GROUP BY domain ORDER BY id DESC) AS L ON P.domain = L.domain ORDER BY domain';
		$list = rex_list::factory($query,20,'domain', 0);
        $list->addParam('domain_start', rex_request('domain_start', 'int'));
		$list->addTableAttribute('class', 'table-striped');
		$list->setNoRowsMessage($this->i18n('dsgvo_server_norows_message'));
		
		// icon column (Domain hinzufügen bzw. bearbeiten)
		$thIcon = '<a href="'.$list->getUrl(['func' => 'domain_add','start' => $start]).'"><i class="rex-icon rex-icon-add-action"></i></a>';
		$tdIcon = '<i class="rex-icon fa-file-text-o"></i>';
		$list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
		$list->setColumnParams($thIcon, ['func' => 'domain_edit', 'id' => '###id###','start' => $start]);
		
		$list->setColumnLabel('domain', $this->i18n('dsgvo_server_domain_column_domain'));
		$list->setColumnParams('domain', ['id' => '###id###','domain' => '###domain###', 'func' => 'domain_edit']);
			
		$list->addColumn($this->i18n('dsgvo_server_domain_column_manage_text'), $this->i18n('dsgvo_server_domain_column_manage_text'), 3);
		$list->setColumnParams($this->i18n('dsgvo_server_domain_column_manage_text'), ['data_id' => '###id###', 'func' => 'domain_details', 'domain' => '###domain###']);

		$list->setColumnLabel('api_key', $this->i18n('dsgvo_server_domain_column_api_key'));
		$list->setColumnFormat('api_key', 'custom', function ($params) {
			return substr($params['list']->getValue('api_key'),0,10)."...";
		});

		$list->setColumnLabel('description', $this->i18n('dsgvo_server_domain_column_description'));
		$list->setColumnFormat('description', 'custom', function ($params) {
			return str_replace(" ", '&nbsp;', $params['list']->getValue('description'));
		});

		$list->setColumnLabel('count_text', $this->i18n('dsgvo_server_domain_column_count_text'));
		$list->setColumnLabel('count_total', $this->i18n('dsgvo_server_domain_column_count_total'));
		$list->setColumnLabel('has_code', $this->i18n('dsgvo_server_domain_column_has_code'));

		$list->setColumnLabel('logdate', $this->i18n('dsgvo_server_domain_column_last_call'));
		$list->setColumnFormat('logdate', 'custom', function ($params) {
			if ($params['list']->getValue('logdate') != "") {
				if($params['list']->getValue('logdate') > $params['list']->getValue('last_change')) {
					return $params['list']->getValue('logdate'); 
				} else {
					return '<a href="http://www.'.$params['list']->getValue('domain').'/dsgvo/dsgvo.inc.php?rex-api-call=dsgvo_sync&api_key='.$params['list']->getValue('api_key').'" target="_blank"><span class="rex-icon fa-exclamation-triangle"></span> '.$params['list']->getValue('logdate').'</a>';
				};
			} else { 
				return '<a href="http://www.'.$params['list']->getValue('domain').'/dsgvo/dsgvo.inc.php?rex-api-call=dsgvo_sync&api_key='.$params['list']->getValue('api_key').'" target="_blank"><span class="rex-icon fa-exclamation-triangle"></span> '.rex_i18n::msg("dsgvo_server_domain_column_last_call_none").'</a>';
			}
		});
		

		$list->addColumn('domain_delete', '<i class="rex-icon rex-icon-delete"></i> ' . $this->i18n('dsgvo_server_domain_column_delete'), -1, ['', '<td class="rex-table-action">###VALUE###</td>']);
		$list->setColumnFormat('domain_delete', 'custom', function ($params) {
			if($params['list']->getValue('domain') == "default") {
				return false;
			} else {
				return '<i class="rex-icon rex-icon-delete"></i> ' . $this->i18n('dsgvo_server_domain_column_delete');
			}
		});
	   	$list->setColumnParams('domain_delete', ['func' => 'domain_delete', 'oid' => '###id###', 'domain' => '###domain###','start' => $start]);
		$list->addLinkAttribute('domain_delete', 'data-confirm', $this->i18n('dsgvo_server_domain_delete_confirm'));


    	$list->removeColumn('id');
		$list->removeColumn('updatedate');

		$list->setColumnSortable('domain');
		$list->setColumnSortable('last_change');
		$list->setColumnSortable('logdate');
		
		$content1 = $list->get();
		
		$fragment = new rex_fragment();
		$fragment->setVar('class', "info", false);
		$fragment->setVar('title', $this->i18n('dsgvo_server_domain_list_title'), false);
		$fragment->setVar('content', $content1, false);
		$content1 = $fragment->parse('core/page/section.php');
		
		echo $content1;
		// Domain-Übersicht ENDE //

	} else if ($func == 'domain_add' || $func == 'domain_edit') { 
		
		// Domain bearbeiten ANFANG //

		$id = rex_request('id', 'int');
		
		if ($func == 'domain_edit') {
			$formLabel = $this->i18n('dsgvo_server_text_edit');
		} elseif ($func == 'domain_add') {
			$formLabel = $this->i18n('dsgvo_server_text_add');
		}
		
		$form = rex_form::factory(rex::getTablePrefix().'dsgvo_server_project', '', 'id='.$id);
        $form->addParam('domain_start', rex_request('domain_start', 'int', 0));

		//Start - add domain-field
		$field = $form->addTextField('domain');
		$field->setLabel($this->i18n('dsgvo_server_domain_column_domain'));
		$field->setNotice($this->i18n('dsgvo_server_domain_column_domain_note'));
		//End - add domain-field

		//Start - add api_key-field
		$field = $form->addTextField('api_key');
		$field->setLabel($this->i18n('dsgvo_server_domain_column_api_key'));
		$field->setNotice($this->i18n('dsgvo_server_domain_column_api_key_note', md5(time())));
		//End - add api_key-field

		//Start - add description-field
		$field = $form->addTextField('description');
		$field->setLabel($this->i18n('dsgvo_server_domain_column_description'));
		$field->setNotice($this->i18n('dsgvo_server_domain_column_description_note'));
		//End - add description-field
		
		if ($func == 'domain_edit') {
			$form->addParam('id', $id);
			$form->addParam('old_domain', $form->getSql()->getValue('domain'));
		}
		$content3 = $form->get();

		$fragment = new rex_fragment();
		$fragment->setVar('class', 'edit', false);
		$fragment->setVar('title', $formLabel, false);
		$fragment->setVar('body', $content3, false);
		$content3 = $fragment->parse('core/page/section.php');

		echo $content3;
		// Domain bearbeiten ENDE //

	} else if ($func == 'domain_details' || $func == 'text_copy_default_de' || $func == 'text_copy_default_en'|| $func == 'text_copy_by_id'|| $func == 'text_delete' || $func == 'set_text_status' || ($func == '' && $result)) {

		if($func == 'text_delete') {
    		$oid = rex_request('oid', 'int');
			$delete = rex_sql::factory()->setQuery('DELETE FROM rex_dsgvo_server WHERE id = :oid',array(':oid' => $oid));
			echo rex_view::success( $this->i18n('dsgvo_server_text_deleted'));
		}	
		if($func == 'text_copy_default_de') {
			$query = 'INSERT INTO rex_dsgvo_server (`domain`, `lang`, `name`, `category`,`keyword`, `text`, `source`, `source_url`, `prio`, `status`, `updatedate`) (SELECT :domain AS `domain`, `lang`, `name`, `category`,`keyword`, `text`, `source`, `source_url`, `prio`, 0 as `status`, NOW() FROM rex_dsgvo_server WHERE domain = "default" AND lang = "de" AND status = 1)';
			rex_sql::factory()->setDebug(0)->setQuery($query, [":domain" => $domain]);
			echo rex_view::success( $this->i18n('dsgvo_server_text_default_copied'));
		}	
		if($func == 'text_copy_default_en') {
			$query = 'INSERT INTO rex_dsgvo_server (`domain`, `lang`, `name`, `category`,`keyword`, `text`, `source`, `source_url`, `prio`, `status`, `updatedate`) (SELECT :domain AS `domain`, `lang`, `name`, `category`,`keyword`, `text`, `source`, `source_url`, `prio`, 0 as `status`, NOW() FROM rex_dsgvo_server WHERE domain = "default" AND lang = "en" AND status = 1)';
			rex_sql::factory()->setDebug(0)->setQuery($query, [":domain" => $domain]);
			echo rex_view::success( $this->i18n('dsgvo_server_text_default_copied'));
		}	
		if($func == 'text_copy_by_id') {
			$query = 'INSERT INTO rex_dsgvo_server (`domain`, `lang`, `name`, `category`,`keyword`, `text`, `source`, `source_url`, `prio`, `status`, `updatedate`) (SELECT :domain AS `domain`, `lang`, `name`, `category`,`keyword`, `text`, `source`, `source_url`, `prio`, 0 as `status`, NOW() FROM rex_dsgvo_server WHERE id=:id)';
			rex_sql::factory()->setDebug(0)->setQuery($query, [":domain" => $domain, ":id" => rex_request('text_copy_id')]);
			echo rex_view::success( $this->i18n('dsgvo_server_text_default_copied'));
		}	
				
		// Offline / Online schalten
		if ($func == 'set_text_status') {
			$status = (rex_request('oldstatus', 'int') + 1) % 2;
			$msg = $status == 1 ? 'dsgvo_server_status_activate' : 'dsgvo_server_status_deactivate';
			$oid = rex_request("oid", "int");
			$update = rex_sql::factory()->setQuery('UPDATE rex_dsgvo_server SET status = :status WHERE id = :oid',array(':status' => $status, ':oid' => $oid))->execute(array(':status' => $status, ':oid' => $oid));
			if ($update) {
				echo rex_view::success($this->i18n($msg . '_success', $name));
			} else {
				echo rex_view::error($this->i18n($msg . '_error', $name));
			}
		}
		
		$list = rex_list::factory('SELECT * FROM `'.rex::getTablePrefix().'dsgvo_server` WHERE domain = "'.$domain.'" ORDER BY `prio` ASC',20,'text',false);
		$list->addParam('domain', $domain);
		$list->addParam('func', $func);
        $list->addParam('log_start', rex_request('log_start', 'int'));
        $list->addParam('text_start', rex_request('text_start', 'int'));
		$list->addTableAttribute('class', 'table-striped');
		$list->setNoRowsMessage($this->i18n('dsgvo_server_norows_message'));

		// icon column
		$thIcon = '<a href="'.$list->getUrl(['func' => 'text_add', 'domain' => $domain,'start' => $start]).'"><i class="rex-icon rex-icon-add-action"></i></a>';
		$tdIcon = '<i class="rex-icon fa-file-text-o"></i>';
		$list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
		$list->setColumnParams($thIcon, ['func' => 'text_edit', 'domain' => $domain, 'id' => '###id###','start' => $start]);
		
		//$list->setColumnLabel('name', $this->i18n('sets_column_name'));
		$list->setColumnLabel('type', $this->i18n('sets_column_type'));		
		$list->setColumnParams('name', ['id' => '###id###', 'func' => 'text_edit', 'domain' => $domain,'start' => $start]);

		$list->setColumnLabel('domain', $this->i18n('dsgvo_server_text_column_domain'));
		
		$list->setColumnLabel('lang', $this->i18n('dsgvo_server_text_column_lang'));
		$list->setColumnLabel('category', $this->i18n('dsgvo_server_text_column_category'));
		$list->setColumnLabel('name', $this->i18n('dsgvo_server_text_column_name'));
		$list->setColumnLabel('source', $this->i18n('dsgvo_server_text_column_source'));
		$list->setColumnLabel('prio', $this->i18n('dsgvo_server_text_column_prio'));
		$list->setColumnLabel('status', $this->i18n('dsgvo_server_text_column_status'));
		$list->setColumnParams('status', ['func' => 'set_text_status', 'oldstatus' => '###status###', 'oid' => '###id###', 'domain' => '###domain###','start' => $start]);
		$list->setColumnFormat('status', 'custom', function ($params) {
			$list = $params['list'];  
			if ($params['value'] == "") {
				$str = rex_i18n::msg('dsgvo_client_text_column_status_invalid');
			} elseif ($params['value'] == 1) {
				$str = $list->getColumnLink('status', '<span class="rex-online"><i class="rex-icon rex-icon-online"></i> ' . rex_i18n::msg('dsgvo_client_text_column_status_is_online') . '</span>');
			} else {
				$str = $list->getColumnLink('status', '<span class="rex-offline"><i class="rex-icon rex-icon-offline"></i> ' . rex_i18n::msg('dsgvo_client_text_column_status_is_offline') . '</span>');
			}
			return $str;
		});
		$list->setColumnLabel('updatedate', $this->i18n('dsgvo_server_text_column_updatedate'));

		$list->addColumn('text_delete', '<i class="rex-icon rex-icon-delete"></i> ' . $this->i18n('dsgvo_server_text_column_delete'), -1, ['', '<td class="rex-table-action">###VALUE###</td>']);
    	$list->setColumnParams('text_delete', ['func' => 'text_delete', 'oid' => '###id###', 'domain' => '###domain###','start' => $start]);
    	$list->addLinkAttribute('text_delete', 'data-confirm', $this->i18n('dsgvo_server_text_delete_confirm'));
		
		$list->removeColumn('keyword');
		$list->removeColumn('id');
		$list->removeColumn('text');
		$list->removeColumn('custom_text');
		$list->removeColumn('source_url');
		$list->removeColumn('code');
		
		$content4 = $list->get();
		
		$fragment = new rex_fragment();
		$fragment->setVar('class', "info", false);
		$fragment->setVar('title', $this->i18n('dsgvo_server_text_title'), false);
		$fragment->setVar('content', $content4, false);
		$content4 = $fragment->parse('core/page/section.php');
		
		echo $content4;

		// LOGS
		$domain = rex_request('domain', 'string', "");
		$list = rex_list::factory('SELECT * FROM rex_dsgvo_server_log WHERE domain = "'.$domain.'" ORDER BY createdate DESC LIMIT 30', 10, 'log');
		$list->addParam('domain', $domain);
		$list->addParam('func', $func);
        $list->addParam('log_start', rex_request('log_start', 'int'));
        $list->addParam('text_start', rex_request('text_start', 'int'));

		$list->setColumnLabel('id', $this->i18n('dsgvo_server_project_log_id'));
		$list->setColumnLabel('domain', $this->i18n('dsgvo_server_project_log_domain'));
		$list->setColumnLabel('status', $this->i18n('dsgvo_server_project_log_status'));
		$list->setColumnLabel('createdate', $this->i18n('dsgvo_server_project_log_createdate'));
		$list->removeColumn('raw');
		
		$fragment = new rex_fragment();
		$fragment->setVar('class', 'default', false);
		$fragment->setVar('title', $this->i18n('dsgvo_server_project_log_title'), false);
		$fragment->setVar('body', $list->get(), false);
		$content5 = $fragment->parse('core/page/section.php');
		echo $content5;
		// LOGS END


		if(rex_request('domain','string') != 'default') {
			$buttons_de = '<a class="btn btn-edit" href="index.php?page=dsgvo/server-edit&func=text_copy_default_de&domain='.$domain.'">' . rex_i18n::msg('dsgvo_server_default_text_copy_de') . '</a>';
			$buttons_en = '<a class="btn btn-edit" href="index.php?page=dsgvo/server-edit&func=text_copy_default_en&domain='.$domain.'">' . rex_i18n::msg('dsgvo_server_default_text_copy_en') . '</a>';
			
			// Vorschau 
			$button_preview = ' <button type="button" class="btn btn-primary" data-toggle="modal" data-target=".bd-preview-modal-lg">Vorschau</button>';
			
			$modal_preview = $button_preview.'<div class="modal bd-preview-modal-lg" tabindex="-1" role="dialog" aria-labelledby="Vorschau" aria-hidden="true">
			<div class="modal-dialog modal-lg">
			  <div class="modal-content">      <div class="modal-header">Vorschau</div>      <div class="modal-body">
			  
			  ';
			
			$dsgvo_pool = array_filter(rex_sql::factory()->setDebug(0)->getArray('SELECT * FROM rex_dsgvo_server WHERE status = 1 AND domain = :domain AND lang = "de" ORDER by prio',[':domain'=> $domain]));
		
			foreach($dsgvo_pool AS $key => $dsgvo_item) {
				$dsgvo_pool[$key]['text'] = markitup::parseOutput ('textile', $dsgvo_item['text']);
			}
		
			$output = new rex_fragment();
			// ggf. Sprache anpassen
			$output->setVar("dsgvo_pool", $dsgvo_pool);
			$output->setVar("lang", $lang);
			$output->setVar("domain", $domain);
			$output->setVar("consent", "Einwilligen");
			$output->setVar("revoke", "Widerrufen");
			$output->setVar("source", "Quelle:");
			$modal_preview .= $output->parse('dsgvo-page.fragment.inc.php');

			$modal_preview .= '</div></div></div></div>';




			// Einzelne Texte kopieren //
			$list = rex_list::factory('SELECT d.* FROM 
			(SELECT id, prio, lang, keyword, name, concat(lang, "_", keyword) AS slug FROM rex_dsgvo_server WHERE domain = "default") d
			LEFT JOIN 
			(SELECT id, prio, lang, keyword, name, concat(lang, "_", keyword) AS slug FROM rex_dsgvo_server WHERE domain = "'.$domain.'") p
			ON d.slug = p.slug
			WHERE p.slug IS NULL ORDER BY lang, prio',100,'',false);
			$list->removeColumn('id');
			$list->removeColumn('slug');
			$thIcon = '';
			$tdIcon = '<i class="rex-icon rex-icon-add-action"></i>';
			$list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
			$list->setColumnParams($thIcon, ['func' => 'text_copy_by_id', 'text_copy_id' => '###id###','domain' => $domain]);
	
			$fragment = new rex_fragment();
			$fragment->setVar('class', 'default', false);
			$fragment->setVar('title', $this->i18n('dsgvo_server_default_title'), false);
			$fragment->setVar('body', $buttons_de." ".$buttons_en . $modal_preview . $list->get(), false);
			$fragment->setVar('footer', $this->i18n('dsgvo_server_default_title'), false);
			$fragment->setVar('header', $this->i18n('dsgvo_server_default_title'), false);

			echo $fragment->parse('core/page/section.php');
	
		}


	} else if ($func == 'text_edit' || $func == 'text_add') { // Wenn von einer Domain Texte verwaltet werden
				
		// Text bearbeiten ANFANG //
		$id = rex_request('id', 'int');
		
		if ($func == 'text_edit') {
			$formLabel = $this->i18n('dsgvo_server_text_edit');
		} elseif ($func == 'text_add') {
			$formLabel = $this->i18n('dsgvo_server_text_add');
		}
		
		$form = rex_form::factory(rex::getTablePrefix().'dsgvo_server', '', 'id='.$id);
        $form->addParam('log_start', rex_request('log_start', 'int'));
        $form->addParam('text_start', rex_request('text_start', 'int'));
		$form->addErrorMessage(REX_FORM_ERROR_VIOLATE_UNIQUE_KEY, rex_i18n::msg('dsgvo_server_text_edit_error_unique'));

		//Start - add status-field 
		$field = $form->addSelectField('category');
		$field->setLabel($this->i18n('dsgvo_server_text_column_category'));
		$select = $field->getSelect();
		$select->setSize(1);
		$select->addOption($this->i18n('dsgvo_server_text_column_category_1'), 1);
		$select->addOption($this->i18n('dsgvo_server_text_column_category_2'), 2);
		$select->addOption($this->i18n('dsgvo_server_text_column_category_3'), 3);
		$select->addOption($this->i18n('dsgvo_server_text_column_category_4'), 4);
		$select->addOption($this->i18n('dsgvo_server_text_column_category_5'), 5);
		$select->addOption($this->i18n('dsgvo_server_text_column_category_6'), 6);
		$select->addOption($this->i18n('dsgvo_server_text_column_category_7'), 7);
		$select->addOption($this->i18n('dsgvo_server_text_column_category_8'), 8);
		$field->setNotice($this->i18n('dsgvo_server_text_column_status_note'));
		//End - add status-field

		//Start - add keyword-field
			$field = $form->addTextField('keyword');
			$field->setLabel($this->i18n('dsgvo_server_text_column_keyword'));
			$field->setNotice($this->i18n('dsgvo_server_text_column_keyword_note'));
			$field->getValidator()->add('notEmpty', '');

		//End - add keyword-field
		
		//Start - add name-field
			$field = $form->addTextField('name');
			$field->setLabel($this->i18n('dsgvo_server_text_column_name'));
			$field->setNotice($this->i18n('dsgvo_server_text_column_name_note'));
			$field->getValidator()->add('notEmpty', '');

		//End - add name-field

		//Start - add domain-field
			$field = $form->addSelectField('domain','',['class'=>'form-control selectpicker']); 
			$field->setLabel($this->i18n('dsgvo_server_text_column_domain'));
			$select = $field->getSelect();
			$select->setSize(1);
			$select->addDBSqlOptions("select domain as name, domain as id FROM rex_dsgvo_server_project ORDER BY domain");
			$select->setSelected($domain);
			$field->setNotice($this->i18n('dsgvo_server_text_column_domain_note'));
			$field->getValidator()->add('notEmpty', '');

		//End - add domain-field

		//Start - add lang-field
			$field = $form->addTextField('lang');
			$field->setLabel($this->i18n('dsgvo_server_text_column_lang'));
			$field->setNotice($this->i18n('dsgvo_server_text_column_lang_note'));
			$field->getValidator()->add('notEmpty', '');

		//End - add lang-field
		
		//Start - add text-field
			$field = $form->addTextAreaField('text');
			$field->setLabel($this->i18n('dsgvo_server_text_column_text'));
			$field->setAttribute('class', 'form-control markitupEditor-textile_dsgvo');
			$field->setNotice($this->i18n('dsgvo_server_text_column_text_note'));
			$field->getValidator()->add('notEmpty', '');
		//End - add text-field

		//Start - add code-field
			$field = $form->addTextAreaField('code');
			$field->setLabel($this->i18n('dsgvo_server_text_column_code'));
			$field->setAttribute('class', 'codemirror form-control');
			$field->setNotice($this->i18n('dsgvo_server_text_column_code_note'));
		//End - add text-field
		
		//Start - add source-field
			$field = $form->addTextField('source');
			$field->setLabel($this->i18n('dsgvo_server_text_column_source'));
			$field->setNotice($this->i18n('dsgvo_server_text_column_source_note'));
		//End - add source-field
		
		//Start - add source_url-field
			$field = $form->addTextField('source_url');
			$field->setLabel($this->i18n('dsgvo_server_text_column_source_url'));
			$field->setNotice($this->i18n('dsgvo_server_text_column_source_url_note'));
			$field->getValidator()->add('url', '');

		//End - add source_url-field

		//Start - add prio-field
			$field = $form->addPrioField('prio');
			$field->setLabel($this->i18n('dsgvo_server_text_column_prio'));
			$field->setLabelField('CONCAT(name, " (", domain,"-" ,lang, ")")');
			$field->setAttribute('class', 'selectpicker form-control');
			$field->setWhereCondition('domain = "'.$domain.'"');
			$field->setNotice($this->i18n('dsgvo_server_text_column_prio_note'));
		//End - add prio-field

		//Start - add status-field 
			$field = $form->addSelectField('status');
			$field->setLabel($this->i18n('dsgvo_server_text_column_status'));
			$select = $field->getSelect();
			$select->setSize(1);
			$select->addOption($this->i18n('dsgvo_server_text_column_status_is_online'), 1);
			$select->addOption($this->i18n('dsgvo_server_text_column_status_is_offline'), 0);
			if ($func == 'text_add') {
				$select->setSelected(1);
			}		
			$field->setNotice($this->i18n('dsgvo_server_text_column_status_note'));
		//End - add status-field
		
		if ($func == 'text_edit') {
			$form->addParam('id', $id);
		}

		$form->addParam("domain", $domain);
		$form->addParam("result", "text_added");

		$content2 = $form->get();

		$fragment = new rex_fragment();
		$fragment->setVar('class', 'edit', false);
		$fragment->setVar('title', $formLabel, false);
		$fragment->setVar('body', $content2, false);
		$content2 = $fragment->parse('core/page/section.php');
		
		echo $content2;
		// Text bearbeiten ENDE //

	}  
?>
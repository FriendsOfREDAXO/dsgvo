<!-- dsgvo-consent-fragment -->
<? if($_COOKIE['dsgvo_cookie_consent'] != 1) { ?>
<div class="dsgvo-cookie_consent">
	<div class="dsgvo-cookie_consent-inner">
		<div class="dsgvo-cookie_consent-info">
			<p class="dsgvo-cookie_consent-text"><?= $this->info; ?> <a class="dsgvo-cookie_consent-more" href="<?= $this->url; ?>"><?= $this->learn_more; ?></a></p>
		</div>
		<div class="dsgvo-cookie_consent-dismiss">
			<button class="dsgvo-cookie_consent-ok" onClick="dsgvoConsent(1);"><?= $this->dismiss; ?></button>
		</div>
	</div>
<script>
function dsgvoConsent(status) {
	if(status == 1) {
		Cookies.set("dsgvo_cookie_consent", 1, { expires: 365 });
		document.querySelectorAll('.dsgvo-cookie_consent')[0].style.display = 'none';
	} else {
		Cookies.set("dsgvo_cookie_consent", -1, { expires: 365 });
	}
	}
</script>
<?= rex_config::get("dsgvo", "dsgvo_consent_css"); ?>
<? } ?>
<!-- / dsgvo-consent-fragment -->
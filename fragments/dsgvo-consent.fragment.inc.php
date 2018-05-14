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
	DsgvoCookies = Cookies.noConflict();

	function dsgvoConsent(status) {
		
		if(status == 1) {
			DsgvoCookies.set("dsgvo_cookie_consent", 1, { expires: 365 });
			document.querySelectorAll('.dsgvo-cookie_consent')[0].style.display = 'none';
			<? if($this->html_padding) { ?>
				document.body.parentNode.style.padding<?= $this->html_padding ?> = "0px";
			<? } ?>
		} else {
			DsgvoCookies.set("dsgvo_cookie_consent", -1, { expires: 365 });
		}
	}
	</script>
	<? if($this->html_padding) { ?>
	</script>
	<script>
			var DsgvoConsentPadding = function (event) {
				document.body.style.padding<?= $this->html_padding ?> = document.querySelectorAll('.dsgvo-cookie_consent')[0].offsetHeight+"px";
			};
			window.onresize = DsgvoConsentPadding;
			window.onload = DsgvoConsentPadding;
		</script>
	<? } ?>
<?= rex_config::get("dsgvo", "dsgvo_consent_css"); ?>
<? } ?>
<!-- / dsgvo-consent-fragment -->
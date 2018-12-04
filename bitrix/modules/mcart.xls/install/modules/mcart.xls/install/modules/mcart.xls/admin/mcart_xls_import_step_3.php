<?IncludeModuleLangFile( __FILE__);?>
<div id="step5-progress-bar">
<?=GetMessage("XLS_WAIT")?>
</div>
<?

?>
<script type="text/javascript">
	function strpos( haystack, needle, offset){
		var i = haystack.indexOf( needle, offset );
		return i >= 0 ? i : false;
	}

	/*function repeat_import() {
		$.ajax({
			url: "/bitrix/include/mcart.xls/mcart_xls_import_step_2_js.php",
			 type: "GET",
			dataType: "json",
			timeout: 50000,
			success: function(data, textStatus){
				$("#step5-progress-bar").html(data);
				if (strpos(data, "The End")) {
					var cntString = data.toString().split ('||');
				}
				else {
					
					setTimeout(repeat_import, 1000);
				}
			},
			complete: function(xhr, textStatus) {
			
			},
			async: true
		});
	}
	*/
	function repeat_import() {
		BX.ajax({
		url: '/bitrix/include/mcart.xls/mcart_xls_import_step_2_js.php',
           data: {},
           method: 'POST',
           dataType: 'html',
           timeout: 500000,
           async: false,
           processData: true,
           scriptsRunFirst: true,
           emulateOnload: true,
           start: true,
           cache: false,
           onsuccess: function(data){
            //BX("step5-progress-bar").innerHTML = data;
				if (parseInt(data)>0) {
					BX("step5-progress-bar").innerHTML = '<?=GetMessage("MCART_WORKING_ELEMENT")?>' + data;
					setTimeout(repeat_import, 1000);
					
				}
				else {
					BX("step5-progress-bar").innerHTML = data;  /*'<?=GetMessage("MCART_FINISH_OK")?>';*/
				}
           },
           onfailure: function(){

           }
		});
	}
	
	$(function (){
		setTimeout(repeat_import, 1000);
	});
</script>
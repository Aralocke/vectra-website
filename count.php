<script type="text/javascript">
function CountCode(){
document.getElementById('codesizex').innerHTML='<h1>'+document.getElementById('codex').value.replace(/^\s*(?:\/\*(?!\*\/)[\s\S]*\*\/|;.+)?|\s*\r?\n?$/mg,'').length+' bytes</h1>';setTimeout(CountCode,600);}setTimeout(CountCode,0);
</script>

<br>
<br>
<form><textarea id="codex" rows="20" cols="60"></textarea></form><div id="codesizex"><h1>0 bytes</h1></div></div>

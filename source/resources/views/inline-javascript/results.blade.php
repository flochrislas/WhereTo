<!-- The Javascript for the details page -->
<script>

/** "Copy URL" button: put the given string in the clipboard */
function copyStringToClipboard (str) {
   console.info("copyStringToClipboard: " + str);
   // Modern API, only available on HTTPS or localhost
   if (navigator.clipboard && window.isSecureContext) {
     navigator.clipboard.writeText(str).catch(e => console.warn("Clipboard write failed: " + e));
     return;
   }
   // Fallback for plain HTTP: put the text in an invisible textarea, select it and copy
   const el = document.createElement('textarea');
   el.value = str;
   el.setAttribute('readonly', '');
   el.style.position = 'absolute';
   el.style.left = '-9999px';
   document.body.appendChild(el);
   el.select();
   document.execCommand('copy');
   document.body.removeChild(el);
}

</script>

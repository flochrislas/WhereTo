<!-- The Javascript for the details page -->
<script>

function copyStringToClipboard (str) {
   console.info("copyStringToClipboard: " + str);
   if (navigator.clipboard && window.isSecureContext) {
     navigator.clipboard.writeText(str).catch(e => console.warn("Clipboard write failed: " + e));
     return;
   }
   // Fallback for non-secure contexts
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

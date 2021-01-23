
<script src="signature_pad.js" type="text/javascript"></script>
<script language="javascript" type="text/javascript">
    $(document).ready(function(){
							   alert('ok')
var canvas = document.querySelector("canvas");

var signaturePad = new SignaturePad(canvas);

// Returns signature image as data URL
signaturePad.toDataURL();

// Draws signature image from data URL
signaturePad.fromDataURL("data:image/png;base64,iVBORw0K...");

// Clears the canvas
signaturePad.clear();

// Returns true if canvas is empty, otherwise returns false
signaturePad.isEmpty();
							   });

</script>
<style>
  .modal-body canvas {
    width: 300px;
    height: 300px;
    border-radius: 4px;
	background-color:#F00;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.02) inset;
  }

</style>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h3>Signature du client</h3>
</div>
<div class="modal-body">
	CONTENU POP-UP<br/>
    <canvas></canvas>
</div>
<div class="modal-footer">
	<button type="button" data-dismiss="modal" class="btn">Close</button>
	<button type="button" class="btn btn-primary">Ok</button>
</div>
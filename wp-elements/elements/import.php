<?php

/**
 * Outputs elements for save, reset, submit
 *
 * 
 */
$val = format_to_edit( json_encode( $this->merge_options() ) );
?>
<a href="#" class="wpe-export-btn btn btn-primary">Export</a>
<a href="#" class="wpe-import-btn btn btn-danger">Import</a>

<div class="wpe-import d-none mt-4">
	<p class='wpe-import-feedback alert large' role='alert'></p>
	<textarea rows="5" name="wpe-import[]" type="textarea" class="form-control"></textarea>
	<p class='alert alert-warning large' role='alert'>Copy your export data into this field and click the continue button below. Your existing data will be lost.</p>
	<button name="wpe-finish-import-btn[]" class="wpe-finish-import-btn btn btn-danger">Continue</button>
</div>

<div class="wpe-export d-none mt-4">
<textarea rows="5" name="wpe-export" type="textarea" class="form-control"><?php echo $val ?></textarea>
<p class='alert alert-info large' role='alert'>Copy the contents of this textarea into a safe place or click the button below to download it.</p>
<a href="data:application/json;charset=utf-8,<?php echo $val ?>" download="settings-export.json" class="wpe-download-export-btn btn btn-primary">Download</a>
</div>

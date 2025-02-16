<?php
use com\cminds\tableofcontents\settings\CMTOC_Settings;
?>
<table class="cmtoc_custom_selctors_wrapper">
	<tbody>
		<tr valign="top">
			<th scope="row">Level 0:</th>
			<th scope="row">Level 1:</th>
			<th scope="row">Level 2:</th>
			<th scope="row">Level 3:</th>
			<th scope="row">Level 4:</th>
			<th scope="row">Level 5:</th>
		</tr>
		<tr valign="top">
			<td>
				<div>
					<span class="cmtoc_custom_selctors_label">Tag:</span><input type="text" disabled name="cmtoc_custom_selectors[0][tag]" value="<?php echo isset( $cmtoc_custom_selectors[ 0 ][ 'tag' ] ) ? $cmtoc_custom_selectors[ 0 ][ 'tag' ] : CMTOC_Settings::get( 'cmtoc_table_of_contentsLevel0Tag' ); ?>" /><br />
					<span class="cmtoc_custom_selctors_label">Class:</span><input type="text" disabled name="cmtoc_custom_selectors[0][class]" value="<?php echo isset( $cmtoc_custom_selectors[ 0 ][ 'class' ] ) ? $cmtoc_custom_selectors[ 0 ][ 'class' ] : CMTOC_Settings::get( 'cmtoc_table_of_contentsLevel0Class' ); ?>" /><br />
					<span class="cmtoc_custom_selctors_label">Id:</span><input type="text" disabled name="cmtoc_custom_selectors[0][id]" value="<?php echo isset( $cmtoc_custom_selectors[ 0 ][ 'id' ] ) ? $cmtoc_custom_selectors[ 0 ][ 'id' ] : CMTOC_Settings::get( 'cmtoc_table_of_contentsLevel0Id' ); ?>" /><br />
				</div>
			</td>
			<td>
				<div>
					<span class="cmtoc_custom_selctors_label">Tag:</span><input type="text" disabled name="cmtoc_custom_selectors[1][tag]" value="<?php echo isset( $cmtoc_custom_selectors[ 1 ][ 'tag' ] ) ? $cmtoc_custom_selectors[ 1 ][ 'tag' ] : CMTOC_Settings::get( 'cmtoc_table_of_contentsLevel1Tag' ); ?>" /><br />
					<span class="cmtoc_custom_selctors_label">Class:</span><input type="text" disabled name="cmtoc_custom_selectors[1][class]" value="<?php echo isset( $cmtoc_custom_selectors[ 1 ][ 'class' ] ) ? $cmtoc_custom_selectors[ 1 ][ 'class' ] : CMTOC_Settings::get( 'cmtoc_table_of_contentsLevel1Class' ); ?>" /><br />
					<span class="cmtoc_custom_selctors_label">Id:</span><input type="text" disabled name="cmtoc_custom_selectors[1][id]" value="<?php echo isset( $cmtoc_custom_selectors[ 1 ][ 'id' ] ) ? $cmtoc_custom_selectors[ 1 ][ 'id' ] : CMTOC_Settings::get( 'cmtoc_table_of_contentsLevel1Id' ); ?>" /><br />
				</div>
			</td>
			<td>
				<div>
					<span class="cmtoc_custom_selctors_label">Tag:</span><input type="text" disabled name="cmtoc_custom_selectors[2][tag]" value="<?php echo isset( $cmtoc_custom_selectors[ 2 ][ 'tag' ] ) ? $cmtoc_custom_selectors[ 2 ][ 'tag' ] : CMTOC_Settings::get( 'cmtoc_table_of_contentsLevel2Tag' ); ?>" /><br />
					<span class="cmtoc_custom_selctors_label">Class:</span><input type="text" disabled name="cmtoc_custom_selectors[2][class]" value="<?php echo isset( $cmtoc_custom_selectors[ 2 ][ 'class' ] ) ? $cmtoc_custom_selectors[ 2 ][ 'class' ] : CMTOC_Settings::get( 'cmtoc_table_of_contentsLevel2Class' ); ?>" /><br />
					<span class="cmtoc_custom_selctors_label">Id:</span><input type="text" disabled name="cmtoc_custom_selectors[2][id]" value="<?php echo isset( $cmtoc_custom_selectors[ 2 ][ 'id' ] ) ? $cmtoc_custom_selectors[ 2 ][ 'id' ] : CMTOC_Settings::get( 'cmtoc_table_of_contentsLevel2Id' ); ?>" /><br />
				</div>
			</td>
			<td>
				<div>
					<span class="cmtoc_custom_selctors_label">Tag:</span><input type="text" disabled name="cmtoc_custom_selectors[3][tag]" value="<?php echo isset( $cmtoc_custom_selectors[ 3 ][ 'tag' ] ) ? $cmtoc_custom_selectors[ 3 ][ 'tag' ] : CMTOC_Settings::get( 'cmtoc_table_of_contentsLevel3Tag' ); ?>" /><br />
					<span class="cmtoc_custom_selctors_label">Class:</span><input type="text" disabled name="cmtoc_custom_selectors[3][class]" value="<?php echo isset( $cmtoc_custom_selectors[ 3 ][ 'class' ] ) ? $cmtoc_custom_selectors[ 3 ][ 'class' ] : CMTOC_Settings::get( 'cmtoc_table_of_contentsLevel3Class' ); ?>" /><br />
					<span class="cmtoc_custom_selctors_label">Id:</span><input type="text" disabled name="cmtoc_custom_selectors[3][id]" value="<?php echo isset( $cmtoc_custom_selectors[ 3 ][ 'id' ] ) ? $cmtoc_custom_selectors[ 3 ][ 'id' ] : CMTOC_Settings::get( 'cmtoc_table_of_contentsLevel3Id' ); ?>" /><br />
				</div>
			</td>
			<td>
				<div>
					<span class="cmtoc_custom_selctors_label">Tag:</span><input type="text" disabled name="cmtoc_custom_selectors[4][tag]" value="<?php echo isset( $cmtoc_custom_selectors[ 4 ][ 'tag' ] ) ? $cmtoc_custom_selectors[ 4 ][ 'tag' ] : CMTOC_Settings::get( 'cmtoc_table_of_contentsLevel4Tag' ); ?>" /><br />
					<span class="cmtoc_custom_selctors_label">Class:</span><input type="text" disabled name="cmtoc_custom_selectors[4][class]" value="<?php echo isset( $cmtoc_custom_selectors[ 4 ][ 'class' ] ) ? $cmtoc_custom_selectors[ 4 ][ 'class' ] : CMTOC_Settings::get( 'cmtoc_table_of_contentsLevel4Class' ); ?>" /><br />
					<span class="cmtoc_custom_selctors_label">Id:</span><input type="text" disabled name="cmtoc_custom_selectors[4][id]" value="<?php echo isset( $cmtoc_custom_selectors[ 4 ][ 'id' ] ) ? $cmtoc_custom_selectors[ 4 ][ 'id' ] : CMTOC_Settings::get( 'cmtoc_table_of_contentsLevel4Id' ); ?>" /><br />
				</div>
			</td>
			<td>
				<div>
					<span class="cmtoc_custom_selctors_label">Tag:</span><input type="text" disabled name="cmtoc_custom_selectors[5][tag]" value="<?php echo isset( $cmtoc_custom_selectors[ 5 ][ 'tag' ] ) ? $cmtoc_custom_selectors[ 5 ][ 'tag' ] : CMTOC_Settings::get( 'cmtoc_table_of_contentsLevel5Tag' ); ?>" /><br />
					<span class="cmtoc_custom_selctors_label">Class:</span><input type="text" disabled name="cmtoc_custom_selectors[5][class]" value="<?php echo isset( $cmtoc_custom_selectors[ 5 ][ 'class' ] ) ? $cmtoc_custom_selectors[ 5 ][ 'class' ] : CMTOC_Settings::get( 'cmtoc_table_of_contentsLevel5Class' ); ?>" /><br />
					<span class="cmtoc_custom_selctors_label">Id:</span><input type="text" disabled name="cmtoc_custom_selectors[5][id]" value="<?php echo isset( $cmtoc_custom_selectors[ 5 ][ 'id' ] ) ? $cmtoc_custom_selectors[ 5 ][ 'id' ] : CMTOC_Settings::get( 'cmtoc_table_of_contentsLevel5Id' ); ?>" /><br />
				</div>
			</td>
		</tr>
	</tbody>
</table>
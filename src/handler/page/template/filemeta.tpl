[ === main === ]
	[ ' navigation ' ]
	<h3>[ ' _t: File ' ] » [ ' mode ' ]</h3>
	[ ' tabs ' ]
	<br>
	[= r _ =
		<div class="file-info">
			<h4>[ ' link ' ]</h4>
			<form action="[ ' href: filemeta ' ]" method="post" name="remove_file">
			[ ' csrf: remove_file ' ]
			<table class="filemeta tbl-fixed">
				<tr>
					<th scope="row">[ ' _t: FileDesc ' ]:</th>
					<td>[ ' file.file_description ' ]</td>
				</tr>
				<tr>
					<td colspan="2">&nbsp;</td>
				</tr>
				<tr>
					<th scope="row">[ ' _t: FileSize ' ]:</th>
					<td>[ ' size ' ]</td>
				</tr>
				<tr>
					<td colspan="2">&nbsp;</td>
				</tr>
				<tr>
					<th scope="row">[ ' _t: UploadBy ' ]:</th>
					<td>[ ' user ' ]</td>
				</tr>
				<tr>
					<th scope="row">[ ' _t: FileAdded ' ]:</th>
					<td>[ ' file.created | time_formatted ' ]</td>
				</tr>
				<tr>
					<td colspan="2">&nbsp;</td>
				</tr>
				<tr>
					<th scope="row">[ ' _t: FileAttachedTo ' ]:</th>
					<td>[ ' location ' ]</td>
				</tr>
				<tr>
					<th scope="row">[ ' _t: FileUsage ' ]:</th>
					<td>[ ' fileusage ' ]</td>
				</tr>
			</table>
			[ ' notice ' ]

			<br>
			<input type="hidden" name="remove" value="">
			<input type="hidden" name="file_id" value="[ ' file.file_id | e attr ' ]">
			[= dontkeep _ =
				<input type="checkbox" id="dontkeep" name="dontkeep">
				<label for="dontkeep">[ ' _t: RemoveDontKeepFile ' ]</label><br>
				<br>
			=]

			<button type="submit" class="btn-danger" name="submit">[ ' _t: DeleteButton ' ]</button>
			&nbsp;
			<a href="[ ' href: ' ]" class="btn-link">
				<button type="button" class="btn-cancel">[ ' _t: CancelButton ' ]</button>
			</a>
			<br>
			<br>
		</form>
		</div>
	=]
	[= l _ =
		<h4>[ ' link ' ]</h4>
		
		<form action="[ ' href: filemeta ' ]" method="post" name="assign_categories">
			[ ' csrf: assign_categories ' ]
			[ ' category ' ]
			<input type="hidden" name="label" value="">
			<input type="hidden" name="file_id" value="[ ' fileid ' ]">
		</form>
	=]
	[= s _ =
		<div class="file-info">
			<h4>[ ' link ' ]</h4>
			[= i _ =
				<span class="show-image"><a href="[ ' href ' ]">[ ' image ' ]</a></span>
			=]
			[= m _ =
				<span class="show-image">[ ' image ' ]</span>
			=]
			<table class="filemeta tbl-fixed">
				<tr>
					<th scope="row">[ ' _t: FileSyntax ' ]:</th>
					<td>
					[= s _ =
						<code>[ ' syntax ' ]</code><br>
						[= d _ =
							<details>
								<summary>[ ' _t: FileAbsolutePath ' ]</summary>
								<code>[ ' syntax ' ]</code><br>
							</details>
						=]
					=]
					</td>
				</tr>
				<tr>
					<th scope="row">[ ' _t: FileDesc ' ]:</th>
					<td><strong>[ ' desc ' ]</strong></td>
				</tr>
				<tr>
					<th scope="row">[ ' _t: FileCaption ' ]:</th>
					<td>[ ' caption | nl2br ' ]</td>
				</tr>
				<tr>
					<td colspan="2">&nbsp;</td>
				</tr>
				<tr>
					<th scope="row">[ ' _t: FileSize ' ]:</th>
					<td>[ ' size ' ]</td>
				</tr>
				[= p _ =
					<tr>
						<th scope="row">[ ' _t: FileDimension ' ]:</th>
						<td>[ ' width | number 0 , . ' ' ] × [ ' height | number 0 , . ' ' ] px</td>
					</tr>
				=]
				<tr>
					<th scope="row">[ ' _t: MimeType ' ]:</th>
					<td>[ ' mime ' ]</td>
				</tr>
				<tr>
					<td colspan="2">&nbsp;</td>
				</tr>
				<tr>
					<th scope="row">[ ' _t: UploadBy ' ]:</th>
					<td>[ ' user ' ]</td>
				</tr>
				<tr>
					<th scope="row">[ ' _t: FileAdded ' ]:</th>
					<td>[ ' created ' ]</td>
				</tr>
				<tr>
					<td colspan="2">&nbsp;</td>
				</tr>
				[= l _ =
					<tr>
						<th scope="row">[ ' _t: License ' ]:</th>
						<td>[ ' license ' ]</td>
					</tr>
				=]
				[= a _ =
					<tr>
						<th scope="row">[ ' _t: FileAuthor ' ]:</th>
						<td>[ ' author ' ]</td>
					</tr>
					<tr>
						<th scope="row">[ ' _t: FileSource ' ]:</th>
						<td>
						[= url _ =
							<a href="[ ' href | e ' ]">
						=]
							[ ' source ' ]
						[= chref _ =
							[ ' nonstatic ' ]
							</a>
						=]
						</td>
					</tr>
					<tr>
						<td colspan="2">&nbsp;</td>
					</tr>
				=]
				<tr>
					<th scope="row">[ ' _t: FileAttachedTo ' ]:</th>
					<td>[ ' location ' ]</td>
				</tr>
				<tr>
					<th scope="row">[ ' _t: FileUsage ' ]:</th>
					<td>[ ' fileusage ' ]</td>
				</tr>
				[= c _ =
					<tr>
						<th scope="row">[ ' _t: Categories ' ]:</th>
						<td>[ ' categories ' ]</td>
					</tr>
				=]
			</table>

			<br>
			<a href="[ ' href: ' ]" class="btn-link">
				<button type="button">[ ' _t: CancelReturnButton ' ]</button>
			</a>
			<br>
			<br>
		</div>
	=]
	[= e _ =
		<div class="file-info">
			<h4>[ ' link ' ]</h4>
			<form action="[ ' href: filemeta ' ]" method="post" name="edit_file">
				[ ' csrf: edit_file ' ]
				<table class="filemeta">
					<tr>
						<th scope="row">
							<label for="UploadDesc">[ ' _t: FileDesc ' ]:</label>
						</th>
						<td>
							<input type="text" id="UploadDesc" name="file_description" maxlength="250" size="80" value="[ ' desc | e attr ' ]">
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="file_caption">[ ' _t: FileCaption ' ]:</label>
						</th>
						<td>
							<textarea id="file_caption" name="caption" rows="6" cols="70">[ ' caption | pre ' ]</textarea>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="license">[ ' _t: License ' ]</label>
						</th>
						<td>
							[ ' license ' ]
						</td>
					</tr>
					<tr>
						<td colspan="2">&nbsp;</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="UploadAuthor">[ ' _t: FileAuthor ' ]</label>
						</th>
						<td>
							<input type="text" id="UploadAuthor" name="author" maxlength="255" size="80" value="[ ' author | e attr ' ]">
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="UploadSource">[ ' _t: FileSource ' ]</label>
						</th>
						<td>
							<input type="text" id="UploadSource" name="source" maxlength="255" size="80" value="[ ' source | e attr ' ]">
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="UploadSourceUrl">[ ' _t: FileSourceUrl ' ]</label>
						</th>
						<td>
							<input type="url" id="UploadSourceUrl" name="source_url" maxlength="255" size="80" value="[ ' url | e attr ' ]">
						</td>
					</tr>
					<tr>
						<td colspan="2">&nbsp;</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="file_lang">[ ' _t: YourLanguage ' ]</label>
						</th>
						<td>
							[ ' lang ' ]
						</td>
					</tr>
				</table>
				<br>
				<input type="hidden" name="edit" value="">
				<input type="hidden" name="file_id" value="[ ' fileid ' ]">
				<button type="submit" class="btn-ok" name="submit">[ ' _t: SaveButton ' ]</button>
				&nbsp;
				<a href="[ ' href: ' ]" class="btn-link">
					<button type="button" class="btn-cancel">[ ' _t: CancelButton ' ]</button>
				</a>
				<br>
				<br>
			</form>
		</div>
	=]

		
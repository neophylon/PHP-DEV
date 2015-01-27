<?php
if( $_REQUEST['Name'] )
{
	if( !is_dir(FILE_ABS.'cms/template/'.$_REQUEST['q'].'/'.$_REQUEST['Name'].'/') ) $FILE->dirMake(FILE_ABS.'cms/template/'.$_REQUEST['q'].'/'.$_REQUEST['Name'].'/'); // 폴더생성
	// 기본폴더설정
	$UTIL->FileManager("cms",'template/'.$_REQUEST['q'].'/'.$_REQUEST['Name'].'/');

	$_REQUEST['Name'] = $CMS->type_replace($_REQUEST['q'],$_REQUEST['Name']);
	
	if( file_exists(FILE_ABS.'cms/template/'.$_REQUEST['q'].'/'.$_REQUEST['Name']) )
	{
		$content = @file_get_contents(FILE_ABS.'cms/template/'.$_REQUEST['q'].'/'.$_REQUEST['Name']);
		// {IMG} 테그변환
	}
}

echo '
	<link rel="stylesheet" href="/js/codemirror/theme/night.css">
	<link rel="stylesheet" href="/js/codemirror/addon/display/fullscreen.css">
	<link rel="stylesheet" href="/js/codemirror/lib/codemirror.css">
	<script src="/js/codemirror/lib/codemirror.js"></script>
	<script src="/js/codemirror/mode/javascript/javascript.js"></script>
	<script src="/js/codemirror/addon/display/fullscreen.js"></script>
	<script src="/js/codemirror/addon/selection/active-line.js"></script>
	<script src="/js/codemirror/addon/edit/matchbrackets.js"></script>
	<style>
	form { position: relative;}
	.CodeMirror-fullscreen { display: block; position: fixed; top: 0; left: 0; width: 100%;z-index: 9999; }
	</style>
	<div class="alert alert-warning"><p>소스코드 에디터 사용시 "F11"은 전체 화면 모드, "ESC"는 전체 화면 모드 해제 입니다.</p></div>
	<textarea id="txtHtml" name="txtHtml" rows="30">'.$content.'</textarea>
	<div class="pull-right p20"><input type="checkbox" class="editorToggle" data-off-color="danger" checked /></div>
	<p class="text-center p20"><button id="btnHtmlSave" class="btn btn-primary"><i class="fa fa-save"></i> 저장</button>	</p>
	<script>
	var editorCheck = true;
	var codeEditor = "";
	$(".editorToggle").bootstrapSwitch({
		onText:"HTML",offColor:"danger",offText:"SOURCE",
		onSwitchChange:function(event,state){
			if(state && !editorCheck)
			{
				codeEditor.toTextArea();
				tinymce.execCommand("mceToggleEditor",false,"txtHtml");
			}
			else if(!state)
			{
				editorCheck = false;
				tinymce.execCommand("mceToggleEditor",false,"txtHtml");
				codeEditor = CodeMirror.fromTextArea(document.getElementById("txtHtml"), {
					mode: "javascript",
					theme: "night",
					lineNumbers: true,
					lineWrapping: true,
					styleActiveLine: true,
					matchBrackets: true,
					extraKeys: {
						"F11": function(cm) {
							cm.setOption("fullScreen", !cm.getOption("fullScreen"));
						},
						"Esc": function(cm) {
							if (cm.getOption("fullScreen")) cm.setOption("fullScreen", false);
						}
					}
				});
				codeEditor.setSize("100%", $("#content").height() - 200);
				$.post("'.$_SERVER['REQUEST_URI'].'",{q:"getContent",w:"'.$_REQUEST['q'].'",Name:"'.$_REQUEST['Name'].'"},function(data){
					codeEditor.setValue(data);
				});
			}
		}
	});
	tinymce.remove("#txtHtml");
	tinymce.init({
		language:"ko_KR",
		selector: "textarea#txtHtml",
		content_css : "/js/bootstrap/dist/css/bootstrap.css,'.FILE_PATH.'cms/style.css",
		forced_root_block : "",
		force_br_newlines : true,
		force_p_newlines : false,
		height:$("#content").height() - 260,
		toolbar_items_size: "small",
		menubar: false,
		relative_urls:false,
		plugins: [ 
			"advlist autolink link image lists charmap print preview hr anchor pagebreak", 
			"searchreplace wordcount visualblocks visualchars code insertdatetime media nonbreaking", 
			"table contextmenu directionality emoticons paste textcolor responsivefilemanager qrcode" 
		],
		image_advtab: true,
		toolbar: "undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | styleselect forecolor backcolor | link unlink anchor | responsivefilemanager image media qrcode | print preview code",
		filemanager_title:"Filemanager",
		external_filemanager_path:"/js/filemanager/",
		external_plugins: { "filemanager" : "/js/filemanager/plugin.min.js"},
		filemanager_access_key:"'.md5($_SESSION['key']).'"
	});
	$("#btnHtmlSave").click(function(){
		var html = "";
		if($(".editorToggle").bootstrapSwitch("state"))
		{
			if(!tinymce.activeEditor.getContent()) { alert("HTML 내용을 입력하세요"); $("#txtHtml").focus(); return false; }
			html = tinymce.activeEditor.getContent();
		}
		else
		{
			html = codeEditor.getValue();
		}
		if(confirm("'.$_POST['Name'].'\n컨텐츠를 저장 하시겠습니까?") )
		{
			$.post("'.$_SERVER['REQUEST_URI'].'",{q:"FileSave",Type:"'.$_REQUEST['q'].'",Name:"'.$_REQUEST['Name'].'",Content:html},function(data){
				if( data == "SUCCESS" )
				{
					alert("저장 되었습니다.");
				} else { alert(data); }
			});
		}
	});
	</script>
';
?>

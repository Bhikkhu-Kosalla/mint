<?php
require_once "../public/_pdo.php";
require_once "../path.php";

if(isset($_GET["id"])){
	$_get_id=$_GET["id"];
}
if(isset($_GET["word"])){
	$_get_word=$_GET["word"];
}
if(isset($_GET["author"])){
	$_get_author=$_GET["author"];
}
else{
	$_get_author="";
}
?>

<?PHP
include "../pcdl/html_head.php";
?>
<body style="margin: 0;padding: 0;" class="reader_body" onload="<?php
if(isset($_get_id)){
echo "wiki_load_id('{$_get_id}')";
}
else if(isset($_get_word)){
echo "wiki_load_word('{$_get_word}')";
}
?>">
	<script src="../term/term.js"></script>
	<script src="../term/note.js"></script>
	<script src="wiki.js"></script>
	<style>
	.term_link,.term_link_new{
		color: blue;
		padding-left: 2px;
		padding-right: 2px;
	}
	.term_link_new{
		color:red;
	}
	#search_result{
		position: absolute;
		background: wheat;
		max-width: 95%;
		width: 24em;
	}
	chapter{
		color: blue;
		text-decoration: none;
		cursor: pointer;
	}
	chapter:hover{
		color: blue;
		text-decoration: underline;
	}
	.icon{
		width: 15px;
		height: 15px;
	}
	.submenu_title{
		font-size: 120%;
		font-weight: 700;		
	}
	.term_word_head_pali {
		text-transform: capitalize;
		font-size: 200%;
		margin: 0.5em 0;
	}
	.term_word_head{
		border-bottom: 1px solid #cecece;
		padding: 5px 0;
	}
	.term_block{
		border-bottom: 1px solid #cecece;
		padding: 5px 0;
	}
	.term_word_head_authors a{
		color: blue;
		margin: 0 3px;
	}
	.term_word_head_authors a:hover{
		text-decoration: underline;
		cursor: pointer;
	}
	note{
		display: block;
		background-color: #80808029;
		padding: 0.5em;
	}
	note .ref{
		text-align: right;
		padding: 5px;
		font-size: 80%;
	}
	.term_block_bar {
		display: flex;
		justify-content: space-between;
	}
	#head_bar{
		display: flex;
    justify-content: space-between;
    height: 5em;
    background-color: var(--bookx);
    border-bottom: 1px solid var(--tool-line-color);
	}
	.term_block_bar_left{
		display: flex;
	}
	.term_block_bar_left_icon{
    display: inline-block;
    width: 1.5em;
    text-align: center;
    height: 1.5em;
    background-color: gray;
    font-size: 180%;
    color: white;
    border-radius: 99px;
	}
	.term_block_bar_left_info{
		    padding-left: 8px;
	}
	.term_meaning{
		font-weight: 700;
	}
	.term_author{
		font-size: 80%;
		color: gray;
	}
	.term_tag{
		font-size: 80%;
		font-weight: 500;
		margin: 0 8px;
	}
	.term_link {
    cursor: pointer;
	}
}

	</style>
<script>
term_word_link_fun("wiki_goto_word");
</script>
<style>
	.index_toolbar{
		position:unset;
	}
	#pali_pedia{
		font-size: 200%;
    margin-top: auto;
    margin-bottom: auto;
    padding-left: 0.5em;
	}
</style>

<?php
    require_once("../pcdl/head_bar.php");
?>
<div id="head_bar" >
	<div id="pali_pedia" style="display:flex;">
		<span>圣典百科</span>
		<span id="wiki_search" style="width:25em;">
			<span style="display:block;">
				<input id="wiki_search_input" type="input" placeholder="search" style="width:30em;background-color: var(--btn-color);"  onkeyup="wiki_search_keyup(event,this)"/>
			</span>
			<span id="search_result">
			</span>
		</span>	
	</div>

	<div>

		<span>
			<a href="#">[设置]</a>
			<a href="#">[建立词条]</a>
			<a href="#">[帮助]</a>
		</span>
	</div>
</div>

<div id="wiki_contents" style="padding: 0 1em;">
loading...
</div>

<button onclick="run()">run</button>
<button onclick="run2()">run2</button>
</body>
</html>
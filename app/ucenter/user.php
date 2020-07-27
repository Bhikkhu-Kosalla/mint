	<!--显示模式-->
	<script>
		var g_langrage="en";
		var g_currLink="";
		function user_init(strPage){
			g_currLink = strPage;
		}

	</script>
	<style>
	#user_info {
		background-color: var(--tool-bg-color2);
	}
	#user_info::after {
    margin-right: 10px;
	}
	.dropdown-content a {
		cursor: pointer;
	}
	#user_info{
		width:20em;
	}
	#user_info_welcome{
    border-bottom: 1px solid var(--tool-line-color);
    padding: 10px;
	}
	#user_info_name{
		font-size:200%;
	}
	#user_info_welcome2{
		text-align:right;
	}
	</style>
		<div class="dropdown" onmouseover="switchMenu(this,'user_info')" onmouseout="hideMenu()">
			<div style="    border: 1px solid var(--btn-border-color);border-radius: 99px;padding-left: 10px;color: var(--btn-color);">
				<span>
				<?php
				if(isset($_COOKIE["userid"])){
					echo $_COOKIE["nickname"];
				}
				else{
					echo "<a href='../ucenter/'>[登陆]</a> <a href='../ucenter/index.php?op=new'>[注册]</a>";
				}
				?>
				</span>
				<button class="dropbtn icon_btn" onClick="switchMenu(this,'user_info')" id="use_mode">	
					<svg class="icon" xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 32 32" id="ic_user_32px" style="fill: var(--tool-link-hover-color);">
						<path d="M20,4A16,16,0,1,0,36,20,16,16,0,0,0,20,4Zm0,4.8a4.8,4.8,0,1,1-4.8,4.8A4.8,4.8,0,0,1,20,8.8Zm0,22.72a11.521,11.521,0,0,1-9.6-5.152c.04-3.176,6.408-4.928,9.6-4.928s9.552,1.752,9.6,4.928A11.521,11.521,0,0,1,20,31.52Z" transform="translate(-4 -4)"/></svg>
				</button>
			</div>
			<div class="dropdown-content" id="user_info">
				<?php
				if(isset($_COOKIE["userid"])){
				?>
				<div id="user_info_welcome">
				<div id="user_info_welcome1"><?php echo $_local->gui->welcome;?></div>
				<div id="user_info_name"><?php echo $_COOKIE["nickname"];?></div>
				<div id="user_info_welcome2"><?php echo $_local->gui->to_the_dhamma;?></div>
				</div>
				<a href="../studio/setting.php" target="_blank">
					<span>
					<svg class="icon">
						<use xlink:href="svg/icon.svg#ic_settings"></use>
					</svg>
						<?php echo $_local->gui->setting;//用户设置?>
					</span>
				</a>
				<a href="../sync" target="_blank">
					<span>
					<svg class="icon">
						<use xlink:href="svg/icon.svg#ic_autorenew_24px"></use>
					</svg>
						<?php echo $_local->gui->sync;//同步数据?>
					</span>
				</a>
				<a href='../ucenter/index.php?op=logout'>
					<svg class="icon">
						<use xlink:href="svg/icon.svg#ic_exit_to_app_24px"></use>
					</svg>
					<?php echo $_local->gui->logout;?>
				</a>
			<?php
				}
			?>				
			</div>

		</div>
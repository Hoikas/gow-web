<?php
/**
 * Writers - Guild of Writers wiki theme based on Monobook.
 */
 
/***********************
* Initialization code. *
***********************/
if( !defined( 'MEDIAWIKI' ) )
	die( -1 );

class SkinWriters extends SkinTemplate {
	var $skinname = 'writers', $stylename = 'writers',
		$template = 'WritersTemplate', $useHeadElement = true;
	function setupSkinUserCss( OutputPage $out ) {
		global $wgHandheldStyle;
		parent::setupSkinUserCss( $out );
		$out->addModuleStyles( 'skins.writers' );
		$out->addStyle("writers/random.php");
	}
}

class WritersTemplate extends BaseTemplate {
	var $skin;
	function execute() {
		$this->skin = $this->data['skin'];
		wfSuppressWarnings();
		$this->html( 'headelement' );

/*************
* HTML code. *
*************/
?><a id="top"></a>
<div id="nav-wrap">
	<div id="nav">
		<ul id="navigation">
			<li><a href="//guildofwriters.org/index.php">Home</a></li>
			<li><a href="//guildofwriters.org/wiki" title="Guild of Writers Wiki">Wiki</a></li>
			<li><a href="//forum.guildofwriters.org" title="Discussion Forum">Forum</a></li>
			<li><a href="/guildofwriters.org/council" title="Guild Council">Council</a></li>
			<li><a href="//guildofwriters.org/journals/" title="Writers&#8217; Development Journals">Development Journals</a></li>
			<li><a href="//guildofwriters.org/cwe-dev" title="CyanWorlds.com Engine Development">Engine Development</a></li>
			<li><a href="//guildofwriters.org/gehn/">Gehn Shard</a></li>
		</ul>
	</div>
</div>
<div id="header">
	<h1>Guild of Writers</h1>
	<a href="/" title="Guild of Writers"><img class="title" src="//guildofwriters.org/wp-content/themes/two/images/title.png" alt="Guild of Writers" /></a>
</div>
<div id="container">
	<div id="content-wrap">
		<div id="sidebar">
			<?php $this->renderPortals( $this->data['sidebar'] ); ?>
			<!-- Meta: Begin -->
			<div class="portal" id="p-personal">
				<h2 class="widgettitle">Meta</h2>
				<div class="body">
					<ul<?php $this->html('userlangattributes') ?>>
						<?php foreach($this->getPersonalTools() as $key => $item) { echo $this->makeListItem($key, $item); } ?>
					</ul>
				</div>
			</div>
			<!-- Meta: End -->
		</div>
		<div class="narrowcolumn">
			<div class="post">
				<!-- Search: Begin -->
				<div id="p-search" class="portal">
					<form action="<?php $this->text( 'wgScript' ) ?>" id="searchform">
						<input type='hidden' name="title" value="<?php $this->text('searchtitle') ?>"/>
						<?php echo $this->makeSearchInput(array( "id" => "searchInput" )); ?>
						<?php echo $this->makeSearchButton("go", array( "id" => "searchGoButton", "class" => "searchButton" )); ?>
						<?php echo $this->makeSearchButton("fulltext", array( "id" => "mw-searchButton", "class" => "searchButton" )); ?>
					</form>
				</div>
				<!-- Search: End -->
				<?php if ( $this->data['sitenotice'] ): ?><div id="siteNotice"><?php $this->html( 'sitenotice' ) ?></div><?php endif; ?>
				<h2 id="title"><?php $this->html( 'title' ) ?></h2>
				<!-- Cactions: Begin -->
				<div id="actions">
					<?php foreach($this->data['content_actions'] as $key => $tab) {
						$linkAttribs = array( 'href' => $tab['href'], 'id' => "ca-$key", 'class' => $tab['class']);
						if( isset( $tab["tooltiponly"] ) && $tab["tooltiponly"] ) {
							$title = Linker::titleAttrib( "ca-$key" );
							if ( $title !== false ) {
								$linkAttribs['title'] = $title;
							}
						} else {
							$linkAttribs += Linker::tooltipAndAccesskeyAttribs( "ca-$key" );
						}
					$linkHtml = Html::element( 'a', $linkAttribs, $tab['text'] );
					echo ' ' . $linkHtml;
					} ?>
				</div>
				<!-- Cactions: End -->
				<div id="contentSub"<?php $this->html( 'userlangattributes' ) ?>><?php $this->html( 'subtitle' ) ?></div>
				<?php if ( $this->data['undelete'] ): ?><div id="contentSub2"><?php $this->html( 'undelete' ) ?></div><?php endif; ?>
				<?php if( $this->data['newtalk'] ): ?><div class="usermessage"><?php $this->html( 'newtalk' )  ?></div><?php endif; ?>
				<?php $this->html( 'bodycontent' ) ?>
				<?php if ( $this->data['catlinks'] ): $this->html( 'catlinks' ); endif; ?>
				<?php if ( $this->data['dataAfterContent'] ): $this->html( 'dataAfterContent' ); endif; ?>
				<div class="visualClear"></div>
			</div>
		</div>	
		<div id="content-bottom"></div>
	</div>
	<div id="footer">
		<p><a href="//guildofwriters.org/index.php">Home</a> | <a href="//www.guildofwriters.org/wiki" title="Guild of Writers Wiki">Wiki</a> | <a href="//forum.guildofwriters.org" title="Discussion Forum">Forum</a> | <a href="//guildofwriters.org/council" title="Guild Council">Council</a> | <a href="//guildofwriters.org/journals/" title="Writers&#8217; Development Journals">Development Journals</a> | <a href="//guildofwriters.org/cwe-dev" title="CyanWorlds.com Engine Development">Engine Development</a> | <a href="//guildofwriters.org/gehn/">Gehn Shard</a> |</p>
	</div>
</div>
<?php $this->printTrail(); ?>
</body>
</html>
<?php
		wfRestoreWarnings();
	}

/********************
* Output functions. *
********************/
	protected function renderPortals( $sidebar ) {
		if ( !isset( $sidebar['SEARCH'] ) ) $sidebar['SEARCH'] = true;
		if ( !isset( $sidebar['TOOLBOX'] ) ) $sidebar['TOOLBOX'] = true;
		if ( !isset( $sidebar['LANGUAGES'] ) ) $sidebar['LANGUAGES'] = true;
		foreach( $sidebar as $boxName => $content ) {
			if ( $content === false )
				continue;
			if ( $boxName == 'SEARCH' ) {
				continue;
			} elseif ( $boxName == 'TOOLBOX' ) { ?>
			<!-- Toolbox: Begin -->
			<div class="portal" id="p-tb">
				<h2 class="widgettitle">Toolbox</h2>
				<div class="body">
					<ul>
						<?php foreach ( $this->getToolbox() as $key => $tbitem ) { echo $this->makeListItem($key, $tbitem); }
						wfRunHooks( 'MonoBookTemplateToolboxEnd', array( &$this ) );
						wfRunHooks( 'SkinTemplateToolboxEnd', array( &$this, true ) );
						?>
					</ul>
				</div>
			</div>
			<!-- Toolbox: End -->
<?php		} elseif ( $boxName == 'LANGUAGES' ) {
				break;
			} else {
				$this->customBox( $boxName, $content );
			}
		}
	}
	
	function customBox( $bar, $cont ) {
		$portletAttribs = array( 'class' => 'generated-sidebar portal', 'id' => Sanitizer::escapeId( "p-$bar" ) );
		$tooltip = Linker::titleAttrib( "p-$bar" );
		if ( $tooltip !== false ) {
			$portletAttribs['title'] = $tooltip;
		}
		echo '	' . Html::openElement( 'div', $portletAttribs );
?>

		<h2 class="widgettitle"><?php $msg = wfMessage( $bar ); echo htmlspecialchars( $msg->exists() ? $msg->text() : $bar ); ?></h2>
		<div class="body">
<?php   if ( is_array( $cont ) ) { ?>
			<ul>
<?php 			foreach($cont as $key => $val) { ?>
				<?php echo $this->makeListItem($key, $val); ?>

<?php			} ?>
			</ul>
<?php   } else {
			print $cont;
		}
?>
		</div>
	</div>
<?php
	}
	
} // End class.

<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:include href="/main.xsl" />

<xsl:template match="main">
	<table width="100%" bgcolor="#FFFFFF" border="0" bordercolor="#000000">
	<tr><td>
		<xsl:apply-templates />
	</td></tr>
	</table>
</xsl:template>

<xsl:template match="addressbook">
	<table width="100%">
	<tr>
	<td>Addressbook</td>
	<td colspan="2"><a href="module.php?module=Kontakt&amp;metod=insert">insert</a></td>
	</tr>
	<xsl:for-each select="kontakt">
		<tr>
		<td><a href="module.php?module=Kontakt&amp;id={id}"><xsl:value-of select="last" /><xsl:text> </xsl:text><xsl:value-of select="first" /></a></td>
		<td><a href="module.php?module=Kontakt&amp;metod=update&amp;id={id}">edit</a></td>
		<td><a href="module.php?module=Kontakt&amp;metod=delete&amp;id={id}">delete</a></td>
		</tr>
	</xsl:for-each>
	</table>
</xsl:template>

<xsl:template match="kontakt">
	<table width="100%">
	<tr bgcolor="#CCCCCC">
	<td width="20%"><a href="module.php?module=Kontakt&amp;id={id}">Main</a><xsl:text> </xsl:text></td>
	<td width="20%"><a href="module.php?module=Kontakt&amp;id={id}&amp;op=home">Home</a></td>
	<td width="20%"><a href="module.php?module=Kontakt&amp;id={id}&amp;op=business">Business</a></td>
	<td width="20%"><a href="module.php?module=Kontakt&amp;id={id}&amp;op=personal">Personal</a></td>
	<td width="20%"><a href="module.php?module=Kontakt&amp;id={id}&amp;op=messinger">Messingers</a></td>
	</tr>
	<tr>
	<td colspan="3">
	<xsl:choose>
		<xsl:when test="user_id != ''">
			<a href="module.php?module=Users&amp;id={user_id}">user</a>
		</xsl:when>
		<xsl:otherwise>
			<form name="form_user_insert" method="post" action="module.php?module=Users&amp;metod=insert">
				<input name="frm_prezime_ime" type ="hidden" value="{last} {first}" />
				<input name="frm_email" type ="hidden" value="{email1}" />
				<input name="frm_kontakt_id" type ="hidden" value="{id}" />
				<A HREF="javascript:document.form_user_insert.submit()">user add</A>
			</form>	
		</xsl:otherwise>
	</xsl:choose>
	</td>
	<td><a href="module.php?module=Kontakt&amp;metod=update&amp;id={id}">edit</a></td>
	<td><a href="module.php?module=Kontakt&amp;metod=delete&amp;id={id}">delete</a></td>
	</tr>
	<xsl:for-each select="*[local-name() != 'user_id']">
		<tr><td colspan="2"><xsl:value-of select="local-name()" /></td><td colspan="3"><xsl:value-of select="." /></td></tr>
	</xsl:for-each>
	</table>
</xsl:template>

<xsl:template match="br">
	<br />
</xsl:template>

</xsl:stylesheet>
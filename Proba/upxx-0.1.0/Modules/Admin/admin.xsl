<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:include href="/main.xsl" />

<xsl:template match="main">
	<xsl:choose>
		<xsl:when test="action/@type != ''">
			<table width="100%">
			<tr bgcolor="#CCCCCC">
			<td><xsl:value-of select="action/@type" /></td></tr>
			<tr><td><xsl:apply-templates /></td></tr>
			</table>	
		</xsl:when>
		<xsl:when test="group != ''">
			<table width="100%">
			<tr bgcolor="#CCCCCC">
			<td>Grupa</td><td>Opis</td>
			<td><a href="module.php?module=Groups&amp;metod=insert">insert</a></td>
			</tr><xsl:apply-templates />
			</table>
		</xsl:when>
		<xsl:when test="user != ''">
			<table width="100%">
			<tr bgcolor="#CCCCCC">
			<td>User</td>
			<td colspan="2" width ="100"><a href="module.php?module=Users&amp;metod=insert">insert</a></td>
			</tr><xsl:apply-templates />
			</table>
		</xsl:when>
	</xsl:choose>
</xsl:template>


<xsl:template match="group">
	<tr><td><xsl:value-of select="group_name" /></td><td><xsl:value-of select="opis" /></td>
	<td><a href="module.php?module=Groups&amp;metod=delete&amp;id={id}">delete</a></td></tr>
</xsl:template>

<xsl:template match="user">
	<tr>
	<td><xsl:value-of select="username" /></td>
	<td><a href="module.php?module=Users&amp;metod=update&amp;id={id}">edit</a></td>
	<td><a href="module.php?module=Users&amp;metod=delete&amp;id={id}">delete</a></td>
	</tr>
	<tr><td colspan="2">
	<xsl:value-of select="prezime_ime" /><br />
	<xsl:value-of select="email" /><br />
	<br />
	<xsl:for-each select="user_group">
		<xsl:value-of select="." /><br />
	</xsl:for-each>
	<br />
	</td></tr>
</xsl:template>

</xsl:stylesheet>
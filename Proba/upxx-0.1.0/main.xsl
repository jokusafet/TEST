<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:output method="html" indent="no"/>
<xsl:template match="/">
<HTML>
<HEAD>
<title>Epro engine</title>
<LINK rev="Stylesheet" href="main.css" type="text/css" rel="Stylesheet" />
</HEAD>
<BODY bgColor="#71828A" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<div align="center">
  <table width="760" border="0" cellpadding="0" cellspacing="0" style="background-color : #F5F5F5">
   <tr border="0" id="header"><td> </td></tr><tr>
    <td><table width="100%" cellpadding="0" cellspacing="0" id="menu">
  		<tr valign="top">
  			<td width="20%"><a href="module.php?module=Groups">Groups</a></td>
			<td width="20%"><a href="module.php?module=Users">User</a></td>
			<td width="20%"><a href="module.php?module=Addressbook">Addressbook</a></td>
			<td width="20%"><a href="module.php?module=Software">Software</a></td>
			<td width="20%"><a href="module.php?module=Software&amp;op=MyLicence">Moje licence</a></td>
		</tr>
	</table></td>
  </tr>
  <tr>
    <td><xsl:apply-templates /></td>
  </tr>
  <tr>
    <td><table width="100%" border="0" cellspacing="0" height="20" cellpadding="4">
	  <tr bgcolor="#D2D2D1"> 
	    <td width="50" height="20"></td>
	    <td height="20"><font color="#000000">PHP engine by utvara :: kontakt <a href="mailto:utvara@ns.sbb.co.yu">utvara</a></font></td>
	  </tr>
	</table></td>
  </tr>
  </table>
</div>
<br />
</BODY></HTML>
</xsl:template>

<xsl:template match="action">
	<xsl:choose>
		<xsl:when test="@type='insert_confirm' or @type='update_confirm'">
			<form name="form_{@module}" method="post" action="module.php?module={@module}&amp;metod={@type}&amp;id={@id}">
			<table width="100%">
			<xsl:for-each select="data_input">
				<xsl:choose>
					<xsl:when test="@name = 'id'">
						<input name="frm_{@name}" type ="hidden" value="{value}" />
					</xsl:when>
					<xsl:when test="@type = 'set'">
						<tr>
						<td><xsl:value-of select="@name" /></td>
						<td>
						<select name="frm_{@name}[]" size="5" multiple="multiple">
						<xsl:for-each select="item">
						    <option value="{.}">
						    	<xsl:if test="@selected='true'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>
						  		<xsl:value-of select="." />
						  	</option>
						</xsl:for-each>
					    </select>
					    </td>
					    </tr>
					</xsl:when>
					<xsl:when test="@type = 'enum'">
						<tr>
						<td><xsl:value-of select="@name" /></td>
						<td>
						<select name="frm_{@name}">
						<xsl:for-each select="item">
						    <option value="{.}">
						    	<xsl:if test="@selected='true'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>
						  		<xsl:value-of select="." />
						  	</option>
						</xsl:for-each>
					    </select>
					    </td>
					    </tr>
					</xsl:when>					
					<xsl:otherwise>
						<tr>
						<td><xsl:value-of select="@name" /></td>
						<xsl:choose>
							<xsl:when test="@len &gt; 256">
								<textarea name="frm_{@name}" cols="75" rows="5"><xsl:value-of select="value" /></textarea>
							</xsl:when>
							<xsl:otherwise>
								<td>
								<input name="frm_{@name}" type ="textfield" value="{value}" maxlength="{@len}">
									<xsl:attribute name="size">
										<xsl:choose>
										<xsl:when test="@len &gt; 75">75</xsl:when>
										<xsl:otherwise><xsl:value-of select ="@len" /></xsl:otherwise>
										</xsl:choose>
									</xsl:attribute>
								</input>
								</td>
							</xsl:otherwise>
						</xsl:choose>
						</tr>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:for-each>
			<xsl:for-each select="hidden">
				<input name="frm_{@name}" type ="hidden" value="{.}" />
			</xsl:for-each>
			<tr><td><input type="reset" name="Reset" value="Reset" /> <input type="submit" name="Submit" value="Submit" /></td></tr>
			</table>
			</form>
		</xsl:when>
		<xsl:when test="@type = 'delete_confirm'">
			<a href="module.php?module={@module}&amp;metod={@type}&amp;id={@id}"><xsl:value-of select="message" /></a>
		</xsl:when>
	</xsl:choose>
</xsl:template>

<xsl:template match="br">
	aaaaa
	<br />
</xsl:template>

</xsl:stylesheet>
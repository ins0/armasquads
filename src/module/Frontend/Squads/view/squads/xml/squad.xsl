<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet
        version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:template match="text()">
        <xsl:value-of select="."/>
    </xsl:template>
    <xsl:template match="*">
        <xsl:apply-templates/>
    </xsl:template>
    <xsl:template match="/">
        <HTML>
            <HEAD>
                <TITLE><xsl:value-of select="/squad/name"/></TITLE>
                <LINK REL="stylesheet" TYPE="text/css" HREF="squad.css"></LINK>
            </HEAD>
            <!--
        XSL template for "squad.xml", used in "Armed Assault" (Ref: "http://www.armedassault.com")
            and "Armed Assault 2" (Ref: "http://www.arma2.com")
        See also the Arma-Wiki page: "http://community.bistudio.com/wiki/squad.xml"
        Created by TomNedry, 26.Jan07
            Reviewed by TomNedry, 15.Jul09
        Lisence: Creative Common (http://creativecommons.org/licenses/by-nc-sa/2.5/deed.de)
        To have a squad logo in the HTML output, just put a "sqd_logo.png" in the same folder...
        -->
            <BODY>
                <!--Main TABLE -->
                <TABLE class="main">
                    <TR>
                        <TD class="left_row" valign="top">
                            <!-- Squad Info Table -->
                            <TABLE class="sqd_info">
                                <TR height="30">
                                    <TD class="one">
                                        <DIV class="sqd_name">
                                            <xsl:value-of select="/squad/name"/>
                                        </DIV>
                                    </TD>
                                </TR>
                                <TR height="30">
                                    <TD class="two">
                                        <DIV class="sqd_title">
                                            <xsl:value-of select="/squad/title"/>
                                        </DIV>
                                    </TD>
                                </TR>
                                <TR height="100%">
                                    <TD width="100%" height="100%" valign="top">
                                        <DIV class="sqd_logo">
                                            <TR height="100%">
                                                <TD width="100%" height="100%" valign="top">
                                                    <DIV class="sqd_logo">
                                                        <img alt="Picture: Squad Logo">
                                                            <xsl:attribute name="src">
                                                                <xsl:value-of select="concat(substring-before(/squad/picture,'.paa'),'.png')"/>
                                                            </xsl:attribute>
                                                        </img>
                                                    </DIV>
                                                </TD>
                                            </TR>
                                        </DIV>
                                    </TD>
                                </TR>
                                <TR height="10">
                                    <TD class="sqd_website">
                                        Web site:
                                        <A>
                                            <xsl:attribute name="href">
                                                http://<xsl:value-of select="/squad/web"/>
                                            </xsl:attribute>
                                            <xsl:value-of select="/squad/web"/>
                                        </A>
                                    </TD>
                                </TR>
                                <TR>
                                    <TD class="sqd_email">
                                        e-mail:
                                        <A>
                                            <xsl:attribute name="href">
                                                mailto:<xsl:value-of select="/squad/email"/>
                                            </xsl:attribute>
                                            <xsl:value-of select="/squad/email"/>
                                        </A>
                                    </TD>
                                </TR>
                            </TABLE>
                            <!-- Squad Info Table -->
                        </TD>
                        <TD class="right_row" valign="top">
                            <!-- Member-Info Table -->
                            <TABLE class="member_info">
                                <Tr>
                                    <Th>Members</Th>
                                    <Th>e-mail</Th>
                                    <Th>ICQ</Th>
                                </Tr>
                                <xsl:for-each select="/squad/member">
                                    <TR>
                                        <xsl:attribute name="class">
                                            <xsl:choose>
                                                <xsl:when test="position() mod 2 = 0">one</xsl:when>
                                                <xsl:otherwise>two</xsl:otherwise>
                                            </xsl:choose>
                                        </xsl:attribute>
                                        <TD class="member_name" rowspan="2">
                                            <xsl:value-of select="name"/>
                                        </TD>
                                        <TD class="member_email">
                                            <A>
                                                <xsl:attribute name="href">
                                                    mailto:<xsl:value-of select="email"/>
                                                </xsl:attribute>
                                                <xsl:value-of select="email"/>
                                            </A>
                                        </TD>
                                        <TD class="member_icq">
                                            <xsl:value-of select="icq"/>
                                        </TD>
                                    </TR>
                                    <TR>
                                        <xsl:attribute name="class">
                                            <xsl:choose>
                                                <xsl:when test="position() mod 2 = 0">one</xsl:when>
                                                <xsl:otherwise>two</xsl:otherwise>
                                            </xsl:choose>
                                        </xsl:attribute>
                                        <TD  class="member_remark" colspan="2" valign="top">
                                            <xsl:value-of select="remark"/>
                                        </TD>
                                    </TR>
                                </xsl:for-each>
                            </TABLE>
                            <!-- Member Info Table -->
                            <!--Main TABLE -->
                        </TD>
                    </TR>
                </TABLE>
                <p style="font-family:verdana;font-size:7pt;color:darkgrey;text-align:center;padding:10px;">
                    "squad.xsl" by <a href="http://www.csar-clan.net">TomNedry</a>, last change: 15.Jul09
                </p>
            </BODY>
        </HTML>
    </xsl:template>
</xsl:stylesheet>
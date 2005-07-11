<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:fo="http://www.w3.org/1999/XSL/Format">

 <xsl:output method="html" encoding="UTF-8"/>
  <xsl:template name="nl2br">
   <xsl:param name="contents" />
   <xsl:choose>
    <xsl:when test="contains($contents, '&#10;')">
    <xsl:value-of select="substring-before($contents, '&#10;')" />
    <br />
    <xsl:call-template name="nl2br">
     <xsl:with-param name="contents" select="substring-after($contents, '&#10;')" />
    </xsl:call-template>
   </xsl:when>
   <xsl:otherwise>
    <xsl:value-of select="$contents" />
   </xsl:otherwise>
  </xsl:choose>
 </xsl:template>
 
 <xsl:template match="/">
		<xsl:variable name="email" select="JobPositionPosting/HowToApply/ApplicationMethods/ByEmail/E-Mail"/>
		<xsl:variable name="web" select="JobPositionPosting/HowToApply/ApplicationMethods/ByWeb/URL"/>
		<xsl:variable name="org_name" select="JobPositionPosting/HiringOrg/HiringOrgName"/>
		<xsl:variable name="org_id" select="JobPositionPosting/HiringOrg/HiringOrgId"/>
		<xsl:variable name="org_unit_desc" select="JobPositionPosting/HiringOrg/OrganizationalUnit/Description"/>
		<xsl:variable name="job_title" select="JobPositionPosting/JobPositionInformation/JobPositionTitle"/>
		<xsl:variable name="job_id" select="JobPositionPosting/JobPositionPostingId"/>
		<xsl:variable name="job_description" select="JobPositionPosting/JobPositionInformation/JobPositionDescription/SummaryText"/>
		<xsl:variable name="job_requirements" select="JobPositionPosting/JobPositionInformation/JobPositionRequirements/SummaryText"/>
		<xsl:variable name="job_date" select="JobPositionPosting/PostDetail/StartDate"/>
		
		<xsl:variable name="address" select="JobPositionPosting/HiringOrg/Contact/PostalAddress/DeliveryAddress/AddressLine"/>
		<xsl:variable name="street" select="JobPositionPosting/HiringOrg/Contact/PostalAddress/DeliveryAddress/StreetName"/>
		<xsl:variable name="building_number" select="JobPositionPosting/HiringOrg/Contact/PostalAddress/DeliveryAddress/BuildingNumber"/>
		<xsl:variable name="postal_code" select="JobPositionPosting/HiringOrg/Contact/PostalAddress/PostalCode"/>
		<xsl:variable name="region" select="JobPositionPosting/HiringOrg/Contact/PostalAddress/Region"/>
		<xsl:variable name="country_code" select="JobPositionPosting/HiringOrg/Contact/PostalAddress/CountryCode"/>
		<html>
			<head>
				<title>
					<xsl:value-of select="JobPositionPosting/JobPositionInformation/JobPositionTitle"/>
				</title>
				<link rel="stylesheet" href="xsl/default.css"/>
			</head>
			<body>
			  <center>
				<table class="panel">
					<tbody>
						<tr>
							<td>
								<table>
									<tbody>
										<tr>
											<td>
												<img alt="logo">
													<xsl:attribute name="src">logos/<xsl:value-of select="$org_id"/>/logo.gif</xsl:attribute>
												</img>
											</td>
										</tr>
										<tr>
											<td>
												<h1>
													<xsl:value-of select="$org_name"/>
												</h1>
											</td>
										</tr>
										<tr>
											<td>
												<xsl:value-of select="$org_unit_desc"/>
											</td>
										</tr>
									</tbody>
								</table>
								<br/>
								<table>
									<tbody>
										<tr>
											<th>Wir suchen:</th>
											<td>
												<xsl:value-of select="$job_title"/>
												(<xsl:value-of select="$job_id"/>)
											</td>
										</tr>
										<tr>
											<th>Beschreibung:</th>
											<td>
<xsl:call-template name="nl2br">
      <xsl:with-param name="contents" select="$job_description" />
</xsl:call-template>											
											</td>
										</tr>
										<tr>
											<th>Vorraussetzungen:</th>
											<td>
												<xsl:value-of select="$job_requirements"/>
											</td>
										</tr>
										<tr>
											<th>Bewerbung:</th>
											<td>
												<xsl:if test="$email">
													<a>
														<xsl:attribute name="href">mailto:<xsl:value-of select="$email"/></xsl:attribute>Emailbewerbung</a>
													<br/>
												</xsl:if>
												<xsl:if test="$web">
													<a>
														<xsl:attribute name="href"><xsl:value-of select="$web"/></xsl:attribute>Onlinebewerbung</a>
													<br/>
												</xsl:if>
											</td>
										</tr>
										<tr>
											<th>Kontaktadresse</th>
											<td>
												<xsl:if test="$address">
													<xsl:value-of select="$address"/><br/>
												</xsl:if>
												<xsl:value-of select="$street"/><xsl:text> </xsl:text>
												<xsl:value-of select="$building_number"/><br/>
												<xsl:value-of select="$postal_code"/><xsl:text> </xsl:text>
												<xsl:value-of select="$region"/><br/>
												<xsl:value-of select="$country_code"/><br/>												
											</td>
										</tr>
										<tr>
											<th>Datum</th>
											<td>
												<xsl:if test="$job_date">
													<xsl:value-of select="$job_date"/><br/>
												</xsl:if>
											</td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
					</tbody>
				</table>
				</center>
			</body>
		</html>
	</xsl:template>
</xsl:stylesheet>

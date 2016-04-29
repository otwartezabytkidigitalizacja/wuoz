<?php
/*
Template Name: OAI-PMH Gateway
*/

$verb = mb_strtolower($_GET['verb']);

switch ($verb) {
    case 'identify':
        oaiIdentify();
        break;
    case 'listmetadataformats':
        oaiListMetadataFormats();
        break;
    case 'listsets':
        oaiListSets();
        break;
    case 'listidentifiers':
        oaiListIdentifiers(array_change_key_case($_GET));
        break;
    case 'listrecords':
        oaiListRecords(array_change_key_case($_GET));
        break;
    case 'getrecord':
        oaiGetRecord(array_change_key_case($_GET));
        break;
    default:
        oaiError('badVerb', 'Value of the verb argument is not a legal OAI-PMH verb.');
}

/**
 * Response for identify verb
 *
 */
function oaiIdentify()
{
    $args = array(
        'post_type'      => array('document', 'monument'),
        'order_by'       => 'date',
        'order'          => 'ASC',
        'posts_per_page' => 1
    );

    $posts = get_posts($args);
    oaiStart('Identify');
    ?>
    <Identify>
        <repositoryName>Otwarte Zabytki Digitalizacja</repositoryName>
        <baseURL><?php the_permalink(); ?></baseURL>
        <protocolVersion>2.0</protocolVersion>
        <adminEmail>kontakt-oz@centrumcyfrowe.pl</adminEmail>
        <earliestDatestamp><?php echo getGmdate($posts[0]->post_date_gmt, true) ?></earliestDatestamp>
        <deletedRecord>no</deletedRecord>
        <granularity>YYYY-MM-DD</granularity>
    </Identify>
    <?php
    oaiEnd();
}

/**
 * Response for ListMetadataFormats verb
 *
 * 2 formats are implemented:
 * - oai_dc
 * - TODO: second format
 */
function oaiListMetadataFormats()
{
    oaiStart('ListMetadataFormats');
    ?>
    <ListMetadataFormats>
        <metadataFormat>
            <metadataPrefix>oai_dc</metadataPrefix>
            <schema>http://www.openarchives.org/OAI/2.0/oai_dc.xsd</schema>
            <metadataNamespace>http://www.openarchives.org/OAI/2.0/oai_dc/</metadataNamespace>
        </metadataFormat>

        <metadataFormat>
            <metadataPrefix>oai_dcterms</metadataPrefix>
            <schema>http://dublincore.org/schemas/xmls/qdc/dcterms.xsd</schema>
            <metadataNamespace>http://purl.org/dc/terms/</metadataNamespace>
        </metadataFormat>
    </ListMetadataFormats>
    <?php
    oaiEnd();
}

/**
 * ListSets verb - the repository does not support sets
 */
function oaiListSets()
{
    oaiError('noSetHierarchy', 'The repository does not support sets.');
}

function oaiListIdentifiers($args)
{
    $metadataPrefix = $args['metadataprefix'];
    $resumptionToken = $args['resumptiontoken'];
    $set = $args['set'];

    if ($metadataPrefix != '' && $resumptionToken != '') {
        oaiError('badArgument', 'Illegal argument: metadataPrefix.');
    }

    if ($resumptionToken == '')
        checkMetadataPrefix($metadataPrefix);

    if ($set) {
        oaiError('noSetHierarchy', 'The repository does not support sets.');
    }

    $args = array(
        'post_type'      => array('document'),
        'order_by'       => 'date',
        'order'          => 'ASC',
        'posts_per_page' => 100,
        'paged'          => 1
    );

    if ($resumptionToken != '') {
        $resumptionData = json_decode(base64_decode($resumptionToken));
        $args['paged'] = $resumptionData->paged;
        $metadataPrefix = $resumptionData->type;
    }

    $query = new WP_Query($args);
    oaiStart('listIdentifiers');
    ?>
    <ListIdentifiers>
        <?php
        while ($query->have_posts()) {
            $query->the_post();

            ?>
            <record>
                <header>
                    <identifier>oai:wuoz.otwartezabytki.pl:<?php echo $query->post->ID; ?></identifier>
                    <datestamp><?php echo getGmdate($query->post->post_date_gmt, true) ?></datestamp>
                </header>
            </record>
            <?php

        }
        if ($query->query_vars['paged'] < $query->max_num_pages) {
            $resumption['type'] = 'oai_dc';
            $resumption['paged'] = $query->query_vars['paged'] + 1;
            $resumption['verb'] = 'listIdentifiers';
            ?>
            <resumptionToken>
                <?php echo base64_encode(json_encode($resumption)); ?>
            </resumptionToken>
            <?php
        }
        ?>
    </ListIdentifiers>
    <?php
    oaiEnd();
}

/**
 * @param $args
 *
 * ListRecords verb
 *
 * resumption token - b64 encoded json with page number and metadataprefix
 */
function oaiListRecords($args)
{
    $metadataPrefix = $args['metadataprefix'];
    $resumptionToken = $args['resumptiontoken'];
    $set = $args['set'];


    if ($metadataPrefix != '' && $resumptionToken != '') {
        oaiError('badArgument', 'Illegal argument: metadataPrefix.');
    }

    if ($resumptionToken == '')
        checkMetadataPrefix($metadataPrefix);

    if ($set) {
        oaiError('noSetHierarchy', 'The repository does not support sets.');
    }

    $args = array(
        'post_type'      => array('document'),
        'order_by'       => 'date',
        'order'          => 'ASC',
        'posts_per_page' => 100,
        'paged'          => 1
    );

    if ($resumptionToken != '') {
        $resumptionData = json_decode(base64_decode($resumptionToken));
        $args['paged'] = $resumptionData->paged;
        $metadataPrefix = $resumptionData->type;
    }

    $query = new WP_Query($args);
    oaiStart('ListRecords');
    ?>
    <ListRecords>
        <?php
        while ($query->have_posts()) {
            $query->the_post();

            ?>
            <record>
                <header>
                    <identifier>oai:wuoz.otwartezabytki.pl:<?php echo $query->post->ID; ?></identifier>
                    <datestamp><?php echo getGmdate($query->post->post_date_gmt, true) ?></datestamp>
                </header>
                <metadata>
                    <?php oaiMetadata($query->post, $metadataPrefix, 'getRecord') ?>
                </metadata>
            </record>
            <?php

        }
        if ($query->query_vars['paged'] < $query->max_num_pages) {
            $resumption['type'] = 'oai_dc';
            $resumption['paged'] = $query->query_vars['paged'] + 1;
            $resumption['verb'] = 'listRecords';
            ?>
            <resumptionToken>
                <?php echo base64_encode(json_encode($resumption)); ?>
            </resumptionToken>
            <?php
        }
        ?>
    </ListRecords>
    <?php
    oaiEnd();

}

/**
 * @param $args
 *
 * GetRecord verb
 *
 * identifier - oai:wuoz.otwartezabytki.pl: + post_id
 */
function oaiGetRecord($args)
{
    $identifier = $args['identifier'];
    $metadataPrefix = $args['metadataprefix'];

    checkMetadataPrefix($metadataPrefix);

    if ( ! $identifier || $identifier == '') {
        oaiError('badArgument', ' The request includes illegal arguments or is missing required arguments.');
    }

    $id = str_replace('oai:wuoz.otwartezabytki.pl:', '', $identifier);
    $post = get_post($id);

    oaiStart();
    ?>
    <GetRecord>
        <record>
            <header>
                <identifier><?php echo $identifier; ?></identifier>
                <datestamp><?php echo getGmdate(); ?></datestamp>
            </header>
            <metadata>
                <?php oaiMetadata($post, $metadataPrefix, 'getRecord'); ?>
            </metadata>
        </record>
    </GetRecord>
    <?php
    oaiEnd();


}

/**
 * @param $error
 * @param $msg
 *
 * Generate error message
 */
function oaiError($error, $msg)
{
    oaiStart();
    ?>
    <error code="<?php echo $error ?>"><?php echo $msg ?></error>
    <?php
    oaiEnd();
}

/**
 * @param string $verb
 *
 * Start OAI XML
 */
function oaiStart($verb = '')
{
    header('Content-type: application/xml; charset="utf-8"');
    echo '<?xml version="1.0" encoding="utf-8"?>';
    echo '<OAI-PMH xmlns="http://www.openarchives.org/OAI/2.0/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.openarchives.org/OAI/2.0/ http://www.openarchives.org/OAI/2.0/OAI-PMH.xsd">';
    echo '<responseDate>' . getGmdate() . '</responseDate>';
    if ($verb != '')
        echo '<request verb="' . $verb . '">' . get_the_permalink() . '</request>';
    else
        echo '<request>' . get_the_permalink() . '</request>';
}

/**
 * end OAI XML
 */
function oaiEnd()
{
    echo '</OAI-PMH>';
    die();
}

/**
 * @param string $time
 * @param bool|false $string
 *
 * @return string
 *
 * Generating the date in
 */
function getGmdate($time = '', $string = false)
{
    if ($time != '') {
        if ($string) {
            return gmdate("Y-m-d\TH:i:s\Z", strtotime($time));
        } else {
            return gmdate("Y-m-d\TH:i:s\Z", $time);
        }
    } else {
        return gmdate("Y-m-d\TH:i:s\Z");
    }
}

function checkMetadataPrefix($metadataPrefix)
{
    if ($metadataPrefix == '') {
        oaiError('badArgument', 'Please provide metadataPrefix argument.');
    }
    if ($metadataPrefix != 'oai_dc' && $metadataPrefix != 'oai_dcterms') {
        oaiError('cannotDisseminateFormat', 'Provided metadataPrefix is not supported by this repository.');
    }
}

function oaiMetadata($post, $metadataFormat = 'oai_dc', $task = 'listRecords')
{

    if ($metadataFormat == 'oai_dc') {
        ?>
        <oai_dc:dc
            xsi:schemaLocation="http://www.openarchives.org/OAI/2.0/oai_dc/ http://www.openarchives.org/OAI/2.0/oai_dc.xsd"
            xmlns:oai_dc="http://www.openarchives.org/OAI/2.0/oai_dc/"
            xmlns:dc="http://purl.org/dc/elements/1.1/">
            <?php
            getRecordDetails($post, $metadataFormat, false);
            if ($task == 'getRecord') {
                getRecordDetails($post, $metadataFormat, true);
            }
            ?>
        </oai_dc:dc>
        <?php
    } else if ($metadataFormat == 'oai_dcterms') {
        ?>
        <oai_dc:dc
            xsi:schemaLocation="http://dublincore.org/schemas/xmls/qdc/dcterms.xsd"
            xmlns:oai_dc="http://www.openarchives.org/OAI/2.0/oai_dc/"
            xmlns:dc="http://purl.org/dc/elements/1.1/"
            xmlns:dcterms="http://purl.org/dc/terms/">

            <?php
            getRecordDetails($post, $metadataFormat, false);
            if ($task == 'getRecord') {

                getRecordDetails($post, $metadataFormat, true);
            }
            ?>

        </oai_dc:dc>
        <?php

    }
}

function getRecordDetails($post, $metadataFormat, $full=false)
{
    $meta = get_post_meta($post->ID);
    $args = array();

    if ($post->post_type == 'document') {

        $pages = sizeof(json_decode($meta['oz_jpegs'][0]));
        $date = '';
        if ($meta['oz_document_date'][0] != '') {
            $date = str_replace('.', '-', $meta['oz_document_date'][0]);
            if (strlen($date) < 10)
                $date = '01-' . $date;
        }
        $documentType = 'text';
        if ($meta['oz_document_type'][0] != 'fotografia') {
            $documentType = 'image';
        }
        $monuments = array();
        if ($meta['oz_monuments'][0] != '') {
            $monuments = json_decode($meta['oz_monuments'][0]);
        }

        if ($metadataFormat == 'oai_dc') {
            if(!$full) {
                $tag['tag'] = 'dc:title';
                $tag['params'] = 'xml:lang="pl"';
                $tag['value'] = $post->post_title;
                array_push($args, $tag);

                $tag['tag'] = 'dc:date';
                $tag['params'] = 'xml:lang="pl"';
                if($date!='')
                    $tag['value'] = getGmdate($date, true);
                else
                    $tag['value'] = getGmdate($post->post_date_gmt, true);
                array_push($args, $tag);

                $tag['tag'] = 'dc:publisher';
                $tag['params'] = '';
                $tag['value'] = 'Centrum Cyfrowe';
                array_push($args, $tag);

                $tag['tag'] = 'dc:language';
                $tag['params'] = '';
                $tag['value'] = 'pol';
                array_push($args, $tag);

                $tag['tag'] = 'dc:identifier';
                $tag['params'] = '';
                $tag['value'] = get_permalink($post->ID);
                array_push($args, $tag);

            } else {

                $tag['tag'] = 'dc:creator';
                $tag['params'] = 'xml:lang="pl"';
                $tag['value'] = $meta['oz_document_creator'][0];
                array_push($args, $tag);

//                $tag['tag'] = 'dc:contributor';
//                $tag['params'] = 'xml:lang="pl"';
//                $tag['value'] = 'Centrum Cyfrowe';
//                array_push($args, $tag);

                $tag['tag'] = 'dc:contributor';
                $tag['params'] = 'xml:lang="pl"';
                $tag['value'] = $meta['oz_document_sponsor'][0];
                array_push($args, $tag);

                $tag['tag'] = 'dc:identifier';
                $tag['params'] = '';
                $tag['value'] = $meta['oz_document_signature'][0];
                array_push($args, $tag);

                $tag['tag'] = 'dc:rights';
                $tag['params'] = 'xml:lang="pl"';
                $tag['value'] = $meta['oz_document_rights'][0];
                array_push($args, $tag);

                $tag['tag'] = 'dc:source';
                $tag['params'] = 'xml:lang="pl"';
                $tag['value'] = $meta['oz_document_creator'][0];
                array_push($args, $tag);

                $descr = $meta['oz_document_description'][0];
                $descrArray = preg_split('/<br[^>]*>/i', $descr);

                if(sizeof($descrArray)>0) {
                    foreach($descrArray as $single) {
                        if($single!='') {
                        $tag['tag'] = 'dc:description';
                        $tag['params'] = 'xml:lang="pl"';
                        $tag['value'] = $single;
                        array_push($args, $tag);
                        }
                    }
                }

                $tag['tag'] = 'dc:type';
                $tag['params'] = '';
                $tag['value'] = $documentType;
                array_push($args, $tag);

                $tag['tag'] = 'dc:type';
                $tag['params'] = 'xml:lang="pl"';
                $tag['value'] = $meta['oz_document_type'][0];
                array_push($args, $tag);

                $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'full' );
                $tag['tag'] = 'dc:source';
                $tag['params'] = '';
                $tag['value'] = $thumb[0];
                array_push($args, $tag);

                if (sizeof($monuments) > 0) {
                    foreach ($monuments as $monument) {
                        $tag['tag'] = 'dc:relation';
                        $tag['params'] = '';
                        $tag['value'] = 'oai:wuoz.otwartezabytki.pl:' . $monument;
                        array_push($args, $tag);

                        $tag['tag'] = 'dc:relation';
                        $tag['params'] = '';
                        $tag['value'] = get_permalink($monument);
                        array_push($args, $tag);
                    }
                    $city = get_post_meta($monument, 'oz_city', true);
                    if ($city != '') {
                        $tag['tag'] = 'dc:coverage.spatial';
                        $tag['params'] = 'xml:lang="pl"';
                        $tag['value'] = $city;
                        array_push($args, $tag);
                    }
                }
            }
        }
        else if ($metadataFormat == 'oai_dcterms') {
            if(!$full) {
                $tag['tag'] = 'dcterms:title';
                $tag['params'] = 'xml:lang="pl"';
                $tag['value'] = $post->post_title;
                array_push($args, $tag);

                $tag['tag'] = 'dcterms:issued';
                $tag['params'] = 'xml:lang="pl"';
                $tag['value'] = getGmdate($post->post_date_gmt, true);
                array_push($args, $tag);

                $tag['tag'] = 'dcterms:dateSubmitted';
                $tag['params'] = 'xml:lang="pl"';
                $tag['value'] = getGmdate($post->post_date_gmt, true);
                array_push($args, $tag);

                $tag['tag'] = 'dcterms:publisher';
                $tag['params'] = '';
                $tag['value'] = 'Centrum Cyfrowe';
                array_push($args, $tag);

                $tag['tag'] = 'dcterms:language';
                $tag['params'] = '';
                $tag['value'] = 'pol';
                array_push($args, $tag);

                $tag['tag'] = 'dcterms:bibliographicCitation';
                $tag['params'] = '';
                $tag['value'] = get_permalink($post->ID);
                array_push($args, $tag);

                $tag['tag'] = 'dcterms:identifier';
                $tag['params'] = '';
                $tag['value'] = get_permalink($post->ID);
                array_push($args, $tag);

            } else {

                $tag['tag'] = 'dcterms:creator';
                $tag['params'] = 'xml:lang="pl"';
                $tag['value'] = $meta['oz_document_creator'][0];
                array_push($args, $tag);

//                $tag['tag'] = 'dcterms:contributor';
//                $tag['params'] = 'xml:lang="pl"';
//                $tag['value'] = 'Centrum Cyfrowe';
//                array_push($args, $tag);

                $tag['tag'] = 'dcterms:contributor';
                $tag['params'] = 'xml:lang="pl"';
                $tag['value'] = $meta['oz_document_sponsor'][0];
                array_push($args, $tag);

                $tag['tag'] = 'dcterms:identifier';
                $tag['params'] = '';
                $tag['value'] = $meta['oz_document_signature'][0];
                array_push($args, $tag);

                $tag['tag'] = 'dcterms:license';
                $tag['params'] = 'xml:lang="pl"';
                $tag['value'] = $meta['oz_document_rights'][0];
                array_push($args, $tag);

                $tag['tag'] = 'dcterms:source';
                $tag['params'] = 'xml:lang="pl"';
                $tag['value'] = $meta['oz_document_creator'][0];
                array_push($args, $tag);

                $tag['tag'] = 'dcterms:medium';
                $tag['params'] = '';
                $tag['value'] = 'pdf';
                array_push($args, $tag);

                $tag['tag'] = 'dcterms:medium';
                $tag['params'] = '';
                $tag['value'] = 'vnd.djvu';
                array_push($args, $tag);

                $tag['tag'] = 'dcterms:medium';
                $tag['params'] = '';
                $tag['value'] = 'jpeg';
                array_push($args, $tag);

                $tag['tag'] = 'dcterms:extent';
                $tag['params'] = '';
                $tag['value'] = $pages;
                array_push($args, $tag);

                $tag['tag'] = 'dcterms:description';
                $tag['params'] = 'xml:lang="pl"';
                $tag['value'] = br2nl($meta['oz_document_description'][0]);
                array_push($args, $tag);

                if($date!='') {
                    $tag['tag'] = 'dcterms:created';
                    $tag['params'] = '';
                    $tag['value'] = getGmdate($date, true);
                    array_push($args, $tag);
                }

                $tag['tag'] = 'dcterms:type';
                $tag['params'] = '';
                $tag['value'] = $documentType;
                array_push($args, $tag);

                if (sizeof($monuments) > 0) {
                    foreach ($monuments as $monument) {
                        $tag['tag'] = 'dcterms:isReferencedBy';
                        $tag['params'] = '';
                        $tag['value'] = 'oai:wuoz.otwartezabytki.pl:' . $monument;
                        array_push($args, $tag);
                    }
                }
            }
        }

    } else if ($post->post_type == 'monument') {
        $documents = array();
        if ($meta['oz_documents'][0] != '') {
            $documents = json_decode($meta['oz_documents'][0]);
        }

        if ($metadataFormat == 'oai_dc') {

            if(!$full) {
                $tag['tag'] = 'dc:title';
                $tag['params'] = 'xml:lang="pl"';
                $tag['value'] = $post->post_title;
                array_push($args, $tag);

                $tag['tag'] = 'dc:date';
                $tag['params'] = 'xml:lang="pl"';
                $tag['value'] = getGmdate($post->post_date_gmt, true);
                array_push($args, $tag);

                $tag['tag'] = 'dc:publisher';
                $tag['params'] = '';
                $tag['value'] = 'Centrum Cyfrowe';
                array_push($args, $tag);

                $tag['tag'] = 'dc:language';
                $tag['params'] = '';
                $tag['value'] = 'pol';
                array_push($args, $tag);

                $tag['tag'] = 'dc:identifier';
                $tag['params'] = '';
                $tag['value'] = get_permalink($post->ID);
                array_push($args, $tag);
            }

            else {
                $tag['tag'] = 'dc:creator';
                $tag['params'] = 'xml:lang="pl"';
                $tag['value'] = 'Centrum Cyfrowe';
                array_push($args, $tag);

                $tag['tag'] = 'dc:type';
                $tag['params'] = '';
                $tag['value'] = 'text';
                array_push($args, $tag);

                if ($meta['oz_address'][0] != '') {
                    $tag['tag'] = 'dc:coverage.spatial';
                    $tag['params'] = 'xml:lang="pl"';
                    $tag['value'] = $meta['oz_address'][0];
                    array_push($args, $tag);

                }

                if ($meta['oz_city'][0] != '') {
                    $tag['tag'] = 'dc:coverage.spatial';
                    $tag['params'] = 'xml:lang="pl"';
                    $tag['value'] = $meta['oz_city'][0];
                    array_push($args, $tag);
                }

                if ($meta['oz_region'][0] != '') {
                    $tag['tag'] = 'dc:coverage.spatial';
                    $tag['params'] = 'xml:lang="pl"';
                    $tag['value'] = $meta['oz_region'][0];
                    array_push($args, $tag);
                }

                if ($meta['oz_lat'][0] != '' && $meta['oz_lng'][0] != '') {
                    $tag['tag'] = 'dc:coverage.x';
                    $tag['params'] = 'scheme="DD"';
                    $tag['value'] = $meta['oz_lat'][0];
                    array_push($args, $tag);

                    $tag['tag'] = 'dc:coverage.y';
                    $tag['params'] = 'scheme="DD"';
                    $tag['value'] = $meta['oz_lng'][0];
                    array_push($args, $tag);
                }

                $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'full' );
                $tag['tag'] = 'dc:source';
                $tag['params'] = '';
                $tag['value'] = $thumb[0];
                array_push($args, $tag);

                if (sizeof($documents) > 0) {
                    foreach ($documents as $document) {
                        $tag['tag'] = 'dc:relation';
                        $tag['params'] = '';
                        $tag['value'] = 'oai:wuoz.otwartezabytki.pl:' . $document;
                        array_push($args, $tag);

                        $tag['tag'] = 'dc:relation';
                        $tag['params'] = '';
                        $tag['value'] = get_permalink($document);
                        array_push($args, $tag);
                    }
                }
            }
        }
        else if ($metadataFormat == 'oai_dcterms') {
            if(!$full) {
                $tag['tag'] = 'dcterms:title';
                $tag['params'] = 'xml:lang="pl"';
                $tag['value'] = $post->post_title;
                array_push($args, $tag);

                $tag['tag'] = 'dcterms:issued';
                $tag['params'] = 'xml:lang="pl"';
                $tag['value'] = getGmdate($post->post_date_gmt, true);
                array_push($args, $tag);

                $tag['tag'] = 'dcterms:dateSubmitted';
                $tag['params'] = 'xml:lang="pl"';
                $tag['value'] = getGmdate($post->post_date_gmt, true);
                array_push($args, $tag);

                $tag['tag'] = 'dcterms:publisher';
                $tag['params'] = '';
                $tag['value'] = 'Centrum Cyfrowe';
                array_push($args, $tag);

                $tag['tag'] = 'dcterms:language';
                $tag['params'] = '';
                $tag['value'] = 'pol';
                array_push($args, $tag);

                $tag['tag'] = 'dcterms:bibliographicCitation';
                $tag['params'] = '';
                $tag['value'] = get_permalink($post->ID);
                array_push($args, $tag);

                $tag['tag'] = 'dcterms:identifier';
                $tag['params'] = '';
                $tag['value'] = get_permalink($post->ID);
                array_push($args, $tag);
            }

            else {
                $tag['tag'] = 'dcterms:creator';
                $tag['params'] = 'xml:lang="pl"';
                $tag['value'] = 'Centrum Cyfrowe';
                array_push($args, $tag);

                if ($meta['oz_address'][0] != '') {
                    $tag['tag'] = 'dcterms:spatial';
                    $tag['params'] = 'xml:lang="pl"';
                    $tag['value'] = $meta['oz_address'][0];
                    array_push($args, $tag);

                }

                if ($meta['oz_city'][0] != '') {
                    $tag['tag'] = 'dcterms:spatial';
                    $tag['params'] = 'xml:lang="pl"';
                    $tag['value'] = $meta['oz_city'][0];
                    array_push($args, $tag);
                }

                if ($meta['oz_region'][0] != '') {
                    $tag['tag'] = 'dcterms:spatial';
                    $tag['params'] = 'xml:lang="pl"';
                    $tag['value'] = $meta['oz_region'][0];
                    array_push($args, $tag);
                }

                if ($meta['oz_lat'][0] != '' && $meta['oz_lng'][0] != '') {
                    $tag['tag'] = 'dc:coverage.x';
                    $tag['params'] = 'scheme="DD"';
                    $tag['value'] = $meta['oz_lat'][0];
                    array_push($args, $tag);

                    $tag['tag'] = 'dc:coverage.y';
                    $tag['params'] = 'scheme="DD"';
                    $tag['value'] = $meta['oz_lng'][0];
                    array_push($args, $tag);
                }

                $tag['tag'] = 'dcterms:type';
                $tag['params'] = '';
                $tag['value'] = 'text';
                array_push($args, $tag);

                if (sizeof($documents) > 0) {
                    foreach ($documents as $document) {
                        $tag['tag'] = 'dcterms:references';
                        $tag['params'] = '';
                        $tag['value'] = 'oai:wuoz.otwartezabytki.pl:' . $document;
                        array_push($args, $tag);
                    }
                }
            }
        }
    }

    generateXMLMeta($args);

}

function br2nl($string)
{
    return preg_replace('/\<br(\s*)?\/?\>/i', "\n", $string);
}

function generateXMLMeta($tags) {
    if(sizeof($tags)>0) {
        foreach($tags as $tag) {
            ?>
            <<?php echo $tag['tag']; ?> <?php echo $tag['params']; ?>>
                <?php echo $tag['value']; ?>
            </<?php echo $tag['tag']; ?>>
            <?php
        }
    }
}


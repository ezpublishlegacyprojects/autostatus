<?php
/*
 * $Id$
 * $HeadURL$
 *
 */

$tpl = eZTemplate::factory();
$ini = eZINI::instance( 'autostatus.ini' );

$Offset = 0;
if ( isset( $Params['Offset'] ) && is_numeric( $Params['Offset'] ) )
{
    $Offset = intval( $Params['Offset'] );
}

$cond = array();
$displayType = 'all';
if ( isset( $Params['Error'] ) )
{
    $errorCode = (int) $Params['Error'];
    if ( $errorCode === 0 )
    {
        $cond = array( 'status' => statusUpdateEvent::NORMAL );
        $displayType = 'succeed';
    }
    elseif ( $errorCode === 1 )
    {
        $cond = array( 'status' => array( array( statusUpdateEvent::ERROR, statusUpdateEvent::EXCEPTION ) ) );
        $displayType = 'error';
    }
    else
    {
        eZDebug::writeWarning( 'Error param is wrong', __METHOD__ );
    }
}

$limit = $ini->variable( 'AutoStatusLogSettings', 'Limit' );
$pageURI = 'autostatus/log';

$list = statusUpdateEvent::fetchList( $cond, $Offset, $limit );
$eventsCount = statusUpdateEvent::fetchListCount( $cond );

$totalCount = statusUpdateEvent::fetchListCount();
$errorCount = statusUpdateEvent::fetchListCount( array( 'status' => array(
    array( statusUpdateEvent::ERROR, statusUpdateEvent::EXCEPTION ) ) )
);
$successCount = statusUpdateEvent::fetchListCount( array( 'status' => statusUpdateEvent::NORMAL ) );

$tpl->setVariable( 'offset', $Offset );
$tpl->setVariable( 'events', $list );
$tpl->setVariable( 'events_count', $eventsCount );
$tpl->setVariable( 'total_count', $totalCount );
$tpl->setVariable( 'error_count', $errorCount );
$tpl->setVariable( 'succeed_count', $successCount );
$tpl->setVariable( 'display_type', $displayType );

$tpl->setVariable( 'limit', $limit );
$tpl->setVariable( 'page_uri', $pageURI );

$Result['path'] = array();
$Result['path'][] = array( 'text' => ezpI18n::tr( 'autostatus/log', 'Auto status log' ),
                           'url'  => $pageURI );
$Result['left_menu'] = 'design:autostatus/menu.tpl';
$Result['content'] = $tpl->fetch( 'design:autostatus/log.tpl' );
?>

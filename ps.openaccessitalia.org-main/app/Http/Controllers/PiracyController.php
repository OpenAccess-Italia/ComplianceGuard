<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;

class PiracyController extends Controller
{
    //
    public function __construct(){
        $this->middleware('auth.piracy');
    }

    public function run(){
        if(env("PIRACY_SHIELD_ENABLED") == "1"){
            $check_env = self::check_env();
            if(count($check_env) == 0){
                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","starting run");
                //get all tickets
                $all_tickets = self::get_all_tickets();
                if($all_tickets !== false){
                    $tickets_ids_list = [];
                    foreach ($all_tickets as $ticket) {
                        $tickets_ids_list[] = $ticket->ticket_id;
                        //check if tickets not already stored
                        $db_ticket = \App\Piracy\Tickets::find($ticket->ticket_id);
                        if(!$db_ticket){
                            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $ticket->ticket_id not stored");
                            //store ticket data
                            $new_db_ticket = new \App\Piracy\Tickets();
                            $new_db_ticket->ticket_id = $ticket->ticket_id;
                            $new_db_ticket->status = $ticket->status;
                            $new_db_ticket->fqdns = json_encode($ticket->fqdn);
                            $new_db_ticket->ipv4s = json_encode($ticket->ipv4);
                            $new_db_ticket->ipv6s = json_encode($ticket->ipv6);
                            $new_db_ticket->metadata = json_encode($ticket->metadata);
                            if($new_db_ticket->save()){
                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $ticket->ticket_id stored");
                                //check if ticket is updatable (not older than 48 hours)
                                if(self::is_updatable($ticket->metadata->created_at)){
                                    //if is updatable
                                    \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $ticket->ticket_id is updatable");
                                    //check if each item is already in blocked list
                                    foreach ($ticket->fqdn as $fqdn) {
                                        if(!\App\Piracy\FQDNs::find($fqdn)){
                                            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $ticket->ticket_id item fqdn $fqdn not stored");
                                            if(self::set_item_processed($fqdn)){
                                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $ticket->ticket_id item fqdn $fqdn set processed");
                                                $new_db_fqdn = new \App\Piracy\FQDNs();
                                                $new_db_fqdn->fqdn = $fqdn;
                                                $new_db_fqdn->original_ticket_id = $ticket->ticket_id;
                                                $new_db_fqdn->save();
                                                $new_log = new \App\Piracy\TicketItemsLog();
                                                $new_log->ticket_id = $ticket->ticket_id;
                                                $new_log->item_type = "fqdn";
                                                $new_log->item = $fqdn;
                                                $new_log->status = "PROCESSED";
                                                $new_log->save();
                                            }else{
                                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $ticket->ticket_id item fqdn $fqdn failed to set processed",true);
                                            }
                                        }else{
                                            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $ticket->ticket_id item fqdn $fqdn already stored");
                                            if(self::set_item_unprocessed($fqdn,"ALREADY_BLOCKED")){
                                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $ticket->ticket_id item fqdn $fqdn set unprocessed (ALREADY_BLOCKED)");
                                                $new_log = new \App\Piracy\TicketItemsLog();
                                                $new_log->ticket_id = $ticket->ticket_id;
                                                $new_log->item_type = "fqdn";
                                                $new_log->item = $fqdn;
                                                $new_log->status = "ALREADY_BLOCKED";
                                                $new_log->save();
                                            }else{
                                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $ticket->ticket_id item fqdn $fqdn failed to set unprocessed (ALREADY_BLOCKED)",true);
                                            }
                                        }
                                    }
                                    foreach ($ticket->ipv4 as $ipv4) {
                                        if(!\App\Piracy\IPv4s::find($ipv4)){
                                            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $ticket->ticket_id item ipv4 $ipv4 not stored");
                                            if(self::set_item_processed($ipv4)){
                                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $ticket->ticket_id item ipv4 $ipv4 set processed");
                                                $new_db_ipv4 = new \App\Piracy\IPv4s();
                                                $new_db_ipv4->ipv4 = $ipv4;
                                                $new_db_ipv4->original_ticket_id = $ticket->ticket_id;
                                                $new_db_ipv4->save();
                                                $new_log = new \App\Piracy\TicketItemsLog();
                                                $new_log->ticket_id = $ticket->ticket_id;
                                                $new_log->item_type = "ipv4";
                                                $new_log->item = $ipv4;
                                                $new_log->status = "PROCESSED";
                                                $new_log->save();
                                            }else{
                                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $ticket->ticket_id item ipv4 $ipv4 failed to set processed",true);
                                            }
                                        }else{
                                            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $ticket->ticket_id item ipv4 $ipv4 already stored");
                                            if(self::set_item_unprocessed($ipv4,"ALREADY_BLOCKED")){
                                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $ticket->ticket_id item ipv4 $ipv4 set unprocessed (ALREADY_BLOCKED)");
                                                $new_log = new \App\Piracy\TicketItemsLog();
                                                $new_log->ticket_id = $ticket->ticket_id;
                                                $new_log->item_type = "ipv4";
                                                $new_log->item = $ipv4;
                                                $new_log->status = "ALREADY_BLOCKED";
                                                $new_log->save();
                                            }else{
                                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $ticket->ticket_id item ipv4 $ipv4 failed to set unprocessed (ALREADY_BLOCKED)",true);
                                            }
                                        }
                                    }
                                    foreach ($ticket->ipv6 as $ipv6) {
                                        if(!\App\Piracy\IPv6s::find($ipv6)){
                                            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $ticket->ticket_id item ipv6 $ipv6 not stored");
                                            if(self::set_item_processed($ipv6)){
                                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $ticket->ticket_id item ipv6 $ipv6 set processed");
                                                $new_db_ipv6 = new \App\Piracy\IPv6s();
                                                $new_db_ipv6->ipv6 = $ipv6;
                                                $new_db_ipv6->original_ticket_id = $ticket->ticket_id;
                                                $new_db_ipv6->save();
                                                $new_log = new \App\Piracy\TicketItemsLog();
                                                $new_log->ticket_id = $ticket->ticket_id;
                                                $new_log->item_type = "ipv6";
                                                $new_log->item = $ipv6;
                                                $new_log->status = "PROCESSED";
                                                $new_log->save();
                                            }else{
                                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $ticket->ticket_id item ipv6 $ipv6 failed to set processed",true);
                                            }
                                        }else{
                                            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $ticket->ticket_id item ipv6 $ipv6 already stored");
                                            if(self::set_item_unprocessed($ipv6,"ALREADY_BLOCKED")){
                                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $ticket->ticket_id item ipv6 $ipv6 set unprocessed (ALREADY_BLOCKED)");
                                                $new_log = new \App\Piracy\TicketItemsLog();
                                                $new_log->ticket_id = $ticket->ticket_id;
                                                $new_log->item_type = "ipv6";
                                                $new_log->item = $ipv6;
                                                $new_log->status = "ALREADY_BLOCKED";
                                                $new_log->save();
                                            }else{
                                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $ticket->ticket_id item ipv6 $ipv6 failed to set unprocessed (ALREADY_BLOCKED)",true);
                                            }
                                        }
                                    }
                                }else{
                                    //if is not updatable
                                    \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $ticket->ticket_id is not updatable");
                                    //check if each item is already in blocked list
                                    foreach ($ticket->fqdn as $fqdn) {
                                        if(!\App\Piracy\FQDNs::find($fqdn)){
                                            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $ticket->ticket_id item fqdn $fqdn not stored");
                                            $new_db_fqdn = new \App\Piracy\FQDNs();
                                            $new_db_fqdn->fqdn = $fqdn;
                                            $new_db_fqdn->original_ticket_id = $ticket->ticket_id;
                                            if($new_db_fqdn->save()){
                                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $ticket->ticket_id item fqdn $fqdn stored");
                                            }else{
                                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $ticket->ticket_id item fqdn $fqdn failed to be stored",true);
                                            }
                                        }else{
                                            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $ticket->ticket_id item fqdn $fqdn already stored");
                                        }
                                    }
                                    foreach ($ticket->ipv4 as $ipv4) {
                                        if(!\App\Piracy\IPv4s::find($ipv4)){
                                            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $ticket->ticket_id item ipv4 $ipv4 not stored");
                                            $new_db_ipv4 = new \App\Piracy\IPv4s();
                                            $new_db_ipv4->ipv4 = $ipv4;
                                            $new_db_ipv4->original_ticket_id = $ticket->ticket_id;
                                            if($new_db_ipv4->save()){
                                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $ticket->ticket_id item ipv4 $ipv4 stored");
                                            }else{
                                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $ticket->ticket_id item ipv4 $ipv4 failed to be stored",true);
                                            }
                                        }else{
                                            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $ticket->ticket_id item ipv4 $ipv4 already stored");
                                        }
                                    }
                                    foreach ($ticket->ipv6 as $ipv6) {
                                        if(!\App\Piracy\IPv6s::find($ipv6)){
                                            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $ticket->ticket_id item ipv6 $ipv6 not stored");
                                            $new_db_ipv6 = new \App\Piracy\IPv6s();
                                            $new_db_ipv6->ipv6 = $ipv6;
                                            $new_db_ipv6->original_ticket_id = $ticket->ticket_id;
                                            if($new_db_ipv6->save()){
                                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $ticket->ticket_id item ipv6 $ipv6 stored");
                                            }else{
                                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $ticket->ticket_id item ipv6 $ipv6 failed to be stored",true);
                                            }
                                        }else{
                                            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $ticket->ticket_id item ipv6 $ipv6 already stored");
                                        }
                                    }
                                }
                            }else{
                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $ticket->ticket_id failed to store",true);
                            }
                        }else{
                            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $ticket->ticket_id already stored");
                            //check if ticket is still editable (not older than 24 hours)
                            if(self::is_editable($ticket->metadata->created_at)){
                                //if is still editable recheck if some items have been added/removed from authority system
                                //fqdn
                                $db_fqdns = json_decode($db_ticket->fqdns);
                                $fqdns_changed = false;
                                //removed
                                foreach($db_fqdns as $db_fqdn) {
                                    if(!in_array($db_fqdn,$ticket->fqdn)){
                                        $fqdns_changed = true;
                                        \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $db_ticket->ticket_id item fqdn $db_fqdn has been removed from authority system",true);
                                        if(\App\Piracy\FQDNs::where("fqdn",$db_fqdn)->where("original_ticket_id",$db_ticket->ticket_id)->delete() > 0){
                                            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $db_ticket->ticket_id item fqdn $db_fqdn has been deleted from local system");
                                        }else{
                                            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $db_ticket->ticket_id item fqdn $db_fqdn failed to be deleted from local system (no fqdn found)",true);
                                        }
                                    }
                                }
                                //added
                                foreach($ticket->fqdn as $fqdn){
                                    if(!\App\Piracy\FQDNs::where("fqdn",$fqdn)->where("original_ticket_id",$db_ticket->ticket_id)->first()){
                                        $fqdns_changed = true;
                                        \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $db_ticket->ticket_id item fqdn $fqdn has been added from authority system",true);
                                    }
                                }
                                if($fqdns_changed){
                                    //if fdqns list has changed update it in database ticket structure
                                    $db_ticket->fqdns = json_encode($ticket->fqdn);
                                    if($db_ticket->save()){
                                        \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $db_ticket->ticket_id fqdns list has been updated in local system");
                                    }else{
                                        \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $db_ticket->ticket_id fqdns list failed to be updated in local system",true);
                                    }
                                }
                                //ipv4
                                $db_ipv4s = json_decode($db_ticket->ipv4s);
                                $ipv4s_changed = false;
                                //removed
                                foreach($db_ipv4s as $db_ipv4) {
                                    if(!in_array($db_ipv4,$ticket->ipv4)){
                                        $ipv4s_changed = true;
                                        \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $db_ticket->ticket_id item ipv4 $db_ipv4 has been removed from authority system",true);
                                        if(\App\Piracy\IPv4s::where("ipv4",$db_ipv4)->where("original_ticket_id",$db_ticket->ticket_id)->delete() > 0){
                                            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $db_ticket->ticket_id item ipv4 $db_ipv4 has been deleted from local system");
                                        }else{
                                            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $db_ticket->ticket_id item ipv4 $db_ipv4 failed to be deleted from local system (no ipv4 found)",true);
                                        }
                                    }
                                }
                                //added
                                foreach($ticket->ipv4 as $ipv4){
                                    if(!\App\Piracy\IPv4s::where("ipv4",$ipv4)->where("original_ticket_id",$db_ticket->ticket_id)->first()){
                                        $ipv4s_changed = true;
                                        \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $db_ticket->ticket_id item ipv4 $ipv4 has been added from authority system",true);
                                    }
                                }
                                if($ipv4s_changed){
                                    //if fdqns list has changed update it in database ticket structure
                                    $db_ticket->ipv4s = json_encode($ticket->ipv4);
                                    if($db_ticket->save()){
                                        \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $db_ticket->ticket_id ipv4s list has been updated in local system");
                                    }else{
                                        \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $db_ticket->ticket_id ipv4s list failed to be updated in local system",true);
                                    }
                                }
                                //ipv6
                                $db_ipv6s = json_decode($db_ticket->ipv6s);
                                $ipv6s_changed = false;
                                //removed
                                foreach($db_ipv6s as $db_ipv6) {
                                    if(!in_array($db_ipv6,$ticket->ipv6)){
                                        $ipv6s_changed = true;
                                        \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $db_ticket->ticket_id item ipv6 $db_ipv6 has been removed from authority system",true);
                                        if(\App\Piracy\IPv6s::where("ipv6",$db_ipv6)->where("original_ticket_id",$db_ticket->ticket_id)->delete() > 0){
                                            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $db_ticket->ticket_id item ipv6 $db_ipv6 has been deleted from local system");
                                        }else{
                                            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $db_ticket->ticket_id item ipv6 $db_ipv6 failed to be deleted from local system (no ipv6 found)",true);
                                        }
                                    }
                                }
                                //added
                                foreach($ticket->ipv6 as $ipv6){
                                    if(!\App\Piracy\IPv6s::where("ipv6",$ipv6)->where("original_ticket_id",$db_ticket->ticket_id)->first()){
                                        $ipv6s_changed = true;
                                        \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $db_ticket->ticket_id item ipv6 $ipv6 has been added from authority system",true);
                                    }
                                }
                                if($ipv6s_changed){
                                    //if fdqns list has changed update it in database ticket structure
                                    $db_ticket->ipv6s = json_encode($ticket->ipv6);
                                    if($db_ticket->save()){
                                        \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $db_ticket->ticket_id ipv6s list has been updated in local system");
                                    }else{
                                        \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $db_ticket->ticket_id ipv6s list failed to be updated in local system",true);
                                    }
                                }
                            }
                            //check if ticket is still updatable (not older than 48 hours)
                            if(self::is_updatable($ticket->metadata->created_at)){
                                //if is still updatable recheck if feedback for each ticket item has been sent
                                $db_ticket_fqdns = json_decode($db_ticket->fqdns);
                                foreach ($db_ticket_fqdns as $fqdn) {
                                    if(!\App\Piracy\TicketItemsLog::where('ticket_id',$ticket->ticket_id)->where('item_type','fqdn')->where('item',$fqdn)->first()){
                                        //if feedback not yet sent than run like an item of a new ticket
                                        \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $ticket->ticket_id item fqdn $fqdn has not yet a sent feedback, rechecking");
                                        if(!\App\Piracy\FQDNs::find($fqdn)){
                                            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $ticket->ticket_id item fqdn $fqdn not stored");
                                            if(self::set_item_processed($fqdn)){
                                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $ticket->ticket_id item fqdn $fqdn set processed");
                                                $new_db_fqdn = new \App\Piracy\FQDNs();
                                                $new_db_fqdn->fqdn = $fqdn;
                                                $new_db_fqdn->original_ticket_id = $ticket->ticket_id;
                                                $new_db_fqdn->save();
                                                $new_log = new \App\Piracy\TicketItemsLog();
                                                $new_log->ticket_id = $ticket->ticket_id;
                                                $new_log->item_type = "fqdn";
                                                $new_log->item = $fqdn;
                                                $new_log->status = "PROCESSED";
                                                $new_log->save();
                                            }else{
                                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $ticket->ticket_id item fqdn $fqdn failed to set processed",true);
                                            }
                                        }else{
                                            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $ticket->ticket_id item fqdn $fqdn already stored");
                                            if(self::set_item_unprocessed($fqdn,"ALREADY_BLOCKED")){
                                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $ticket->ticket_id item fqdn $fqdn set unprocessed (ALREADY_BLOCKED)");
                                                $new_log = new \App\Piracy\TicketItemsLog();
                                                $new_log->ticket_id = $ticket->ticket_id;
                                                $new_log->item_type = "fqdn";
                                                $new_log->item = $fqdn;
                                                $new_log->status = "ALREADY_BLOCKED";
                                                $new_log->save();
                                            }else{
                                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $ticket->ticket_id item fqdn $fqdn failed to set unprocessed (ALREADY_BLOCKED)",true);
                                            }
                                        }
                                    }
                                }
                                $db_ticket_ipv4s = json_decode($db_ticket->ipv4s);
                                foreach ($db_ticket_ipv4s as $ipv4) {
                                    if(!\App\Piracy\TicketItemsLog::where('ticket_id',$ticket->ticket_id)->where('item_type','ipv4')->where('item',$ipv4)->first()){
                                        //if feedback not yet sent than run like an item of a new ticket
                                        \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $ticket->ticket_id item ipv4 $ipv4 has not yet a sent feedback, rechecking");
                                        if(!\App\Piracy\IPv4s::find($ipv4)){
                                            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $ticket->ticket_id item ipv4 $ipv4 not stored");
                                            if(self::set_item_processed($ipv4)){
                                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $ticket->ticket_id item ipv4 $ipv4 set processed");
                                                $new_db_ipv4 = new \App\Piracy\IPv4s();
                                                $new_db_ipv4->ipv4 = $ipv4;
                                                $new_db_ipv4->original_ticket_id = $ticket->ticket_id;
                                                $new_db_ipv4->save();
                                                $new_log = new \App\Piracy\TicketItemsLog();
                                                $new_log->ticket_id = $ticket->ticket_id;
                                                $new_log->item_type = "ipv4";
                                                $new_log->item = $ipv4;
                                                $new_log->status = "PROCESSED";
                                                $new_log->save();
                                            }else{
                                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $ticket->ticket_id item ipv4 $ipv4 failed to set processed",true);
                                            }
                                        }else{
                                            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $ticket->ticket_id item ipv4 $ipv4 already stored");
                                            if(self::set_item_unprocessed($ipv4,"ALREADY_BLOCKED")){
                                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $ticket->ticket_id item ipv4 $ipv4 set unprocessed (ALREADY_BLOCKED)");
                                                $new_log = new \App\Piracy\TicketItemsLog();
                                                $new_log->ticket_id = $ticket->ticket_id;
                                                $new_log->item_type = "ipv4";
                                                $new_log->item = $ipv4;
                                                $new_log->status = "ALREADY_BLOCKED";
                                                $new_log->save();
                                            }else{
                                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $ticket->ticket_id item ipv4 $ipv4 failed to set unprocessed (ALREADY_BLOCKED)",true);
                                            }
                                        }
                                    }
                                }
                                $db_ticket_ipv6s = json_decode($db_ticket->ipv6s);
                                foreach ($db_ticket_ipv6s as $ipv6) {
                                    if(!\App\Piracy\TicketItemsLog::where('ticket_id',$ticket->ticket_id)->where('item_type','ipv6')->where('item',$ipv6)->first()){
                                        //if feedback not yet sent than run like an item of a new ticket
                                        \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $ticket->ticket_id item ipv6 $ipv6 has not yet a sent feedback, rechecking");
                                        if(!\App\Piracy\IPv6s::find($ipv6)){
                                            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $ticket->ticket_id item ipv6 $ipv6 not stored");
                                            if(self::set_item_processed($ipv6)){
                                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $ticket->ticket_id item ipv6 $ipv6 set processed");
                                                $new_db_ipv6 = new \App\Piracy\IPv6s();
                                                $new_db_ipv6->ipv6 = $ipv6;
                                                $new_db_ipv6->original_ticket_id = $ticket->ticket_id;
                                                $new_db_ipv6->save();
                                                $new_log = new \App\Piracy\TicketItemsLog();
                                                $new_log->ticket_id = $ticket->ticket_id;
                                                $new_log->item_type = "ipv6";
                                                $new_log->item = $ipv6;
                                                $new_log->status = "PROCESSED";
                                                $new_log->save();
                                            }else{
                                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $ticket->ticket_id item ipv6 $ipv6 failed to set processed",true);
                                            }
                                        }else{
                                            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $ticket->ticket_id item ipv6 $ipv6 already stored");
                                            if(self::set_item_unprocessed($ipv6,"ALREADY_BLOCKED")){
                                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $ticket->ticket_id item ipv6 $ipv6 set unprocessed (ALREADY_BLOCKED)");
                                                $new_log = new \App\Piracy\TicketItemsLog();
                                                $new_log->ticket_id = $ticket->ticket_id;
                                                $new_log->item_type = "ipv6";
                                                $new_log->item = $ipv6;
                                                $new_log->status = "ALREADY_BLOCKED";
                                                $new_log->save();
                                            }else{
                                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $ticket->ticket_id item ipv6 $ipv6 failed to set unprocessed (ALREADY_BLOCKED)",true);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    //negative check if ticket has been removed from authority system (last 24 hours)
                    $last24h_db_tickets = \App\Piracy\Tickets::where("timestamp",">=",\Carbon\Carbon::now()->subHours(24)->toDateTimeString())->get();
                    foreach($last24h_db_tickets as $ticket_to_check) {
                        if(!in_array($ticket_to_check->ticket_id,$tickets_ids_list)){
                            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $ticket_to_check->ticket_id has been removed from authority system",true);
                            $deleted_fqdns = \App\Piracy\FQDNs::where("original_ticket_id",$ticket_to_check->ticket_id)->delete();
                            $deleted_ipv4s = \App\Piracy\IPv4s::where("original_ticket_id",$ticket_to_check->ticket_id)->delete();
                            $deleted_ipv6s = \App\Piracy\IPv6s::where("original_ticket_id",$ticket_to_check->ticket_id)->delete();
                            $deleted_ticket = $ticket_to_check->ticket_id;
                            $ticket_to_check->delete();
                            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","ticket $deleted_ticket has been removed from local system (fqdns: $deleted_fqdns, ipv4s: $deleted_ipv4s, ipv6s: $deleted_ipv6s)",true);
                        }
                    }
                }else{
                    \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","tickets download failed",true);
                }
                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","run ended");
            }else{
                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_cron","run not started because of: ".implode(", ",$check_env),true);
            }
        }
    }

    public function test(){
        $obj = new \StdClass();
        //env
        $env_test = self::check_env();
        $obj->settings = new \StdClass();
        $obj->settings->passed = (count($env_test) == 0);
        $obj->settings->messages = (count($env_test) == 0) ? ["Settings formally correct"] : $env_test;
        //hosts file
        if($obj->settings->passed){
            $obj->hosts_file = new \StdClass();
            $fqdn = parse_url(env('PIRACY_SHIELD_API_URL'), PHP_URL_HOST);
            $dns_resolution = self::check_dns_resolution($fqdn);
            if($dns_resolution !== false){
                $obj->hosts_file->passed = true;
                $obj->hosts_file->messages = ["FQDN $fqdn resolved as $dns_resolution"];
            }else{
                $obj->hosts_file->passed = false;
                $obj->hosts_file->messages = ["FQDN $fqdn can not resolve, you have to put in hosts file the resolution of $fqdn as the IP address provided by the authority"];
            }
            //api status
            if($obj->hosts_file->passed){
                $obj->api_status = new \StdClass();
                if(self::ping()){
                    $obj->api_status->passed = true;
                    $obj->api_status->messages = ["API system is responsive"];
                }else{
                    $obj->api_status->passed = false;
                    $obj->api_status->messages = ["API system is not responsive (view action log for more infos)"];
                }
                //credentials
                if($obj->api_status->passed){
                    $obj->credentials = new \StdClass();
                    if(self::new_login()){
                        $obj->credentials->passed = true;
                        $obj->credentials->messages = ["Authenticated"];
                    }else{
                        $obj->credentials->passed = false;
                        $obj->credentials->messages = ["Not authenticated (view action log for more infos)"];
                    }
                }
            }
        }
        return json_encode($obj);
    }

    public function datatable_tickets(Request $request){
        if($request->ajax()){
            $data = \App\Piracy\Tickets::orderBy("timestamp","desc")->get();
            return Datatables::of($data)
                ->rawColumns(
                    ['status','timestamp']
                )->addColumn('ticket_id',function($row){
                    return '<a href="/piracy/ticket/'.$row->ticket_id.'">'.$row->ticket_id.'</a>';
                })->addColumn('fqdn_count',function($row){
                    return count(json_decode($row->fqdns));
                })->addColumn('ipv4_count',function($row){
                    return count(json_decode($row->ipv4s));
                })->addColumn('ipv6_count',function($row){
                    return count(json_decode($row->ipv6s));
                })->escapeColumns('ticket_id')->make(true);
        }
    }

    public function delete_from_whitelist(Request $request){
        if($request->filled(["item","genre"])){
            $item = $request->input("item");
            $genre = $request->input("genre");
            \App\Http\Controllers\Admin\ActionLogController::log(\Auth::user()->id,\Auth::user()->name,"trynig to delete from ps whitelist $item ($genre)");
            if(self::delete_whitelist($genre,$item)){
                \App\Http\Controllers\Admin\ActionLogController::log(\Auth::user()->id,\Auth::user()->name,"succeded to delete from ps whitelist $item ($genre)");
                return response('',200);
            }else{
                \App\Http\Controllers\Admin\ActionLogController::log(\Auth::user()->id,\Auth::user()->name,"failed to delete from ps whitelist $item ($genre)");
                return response('',500);
            }
        }
    }

    public function add_to_whitelist(Request $request){
        if($request->filled(["item","genre","attr"])){
            \App\Http\Controllers\Admin\ActionLogController::log(\Auth::user()->id,\Auth::user()->name,"trynig to add to ps whitelist ".$request->input("item")." (".$request->input("genre").")");
            switch($request->input("genre")) {
                case 'ipv4':
                case 'ipv6':
                case 'cidr_ipv4':
                case 'cidr_ipv6':
                    if(self::add_whitelist($request->input("genre"),$request->input("item"),"as_code",$request->input("attr"))){
                        \App\Http\Controllers\Admin\ActionLogController::log(\Auth::user()->id,\Auth::user()->name,"succeded to add to ps whitelist ".$request->input("item")." (".$request->input("genre").")");
                        return response('',200);
                    }else{
                        \App\Http\Controllers\Admin\ActionLogController::log(\Auth::user()->id,\Auth::user()->name,"failed to add to ps whitelist ".$request->input("item")." (".$request->input("genre").")");
                        return response('',500);
                    }
                break;
                case 'fqdn':
                    if(self::add_whitelist($request->input("genre"),$request->input("item"),"registrar",$request->input("attr"))){
                        \App\Http\Controllers\Admin\ActionLogController::log(\Auth::user()->id,\Auth::user()->name,"succeded to add to ps whitelist ".$request->input("item")." (".$request->input("genre").")");
                        return response('',200);
                    }else{
                        \App\Http\Controllers\Admin\ActionLogController::log(\Auth::user()->id,\Auth::user()->name,"failed to add to ps whitelist ".$request->input("item")." (".$request->input("genre").")");
                        return response('',500);
                    }
                break;
                default:
                    \App\Http\Controllers\Admin\ActionLogController::log(\Auth::user()->id,\Auth::user()->name,"failed to add to ps whitelist ".$request->input("item")." (".$request->input("genre").")");
                    return response('',500);
                break;
            }
        }
        return response('',500);
    }

    public function view_ticket(Request $request,$ticket_id){
        $ticket = \App\Piracy\Tickets::find($ticket_id);
        if($ticket){
            return view('piracy.ticket.view',['ticket' => $ticket]);
        }
        return response('',404);
    }

    public function datatable_fqdn(Request $request){
        if($request->ajax()){
            $data = \App\Piracy\FQDNs::orderBy("timestamp","desc")->get();
            return Datatables::of($data)
                ->rawColumns(
                    ['fqdn','timestamp']
                )->addColumn('original_ticket_id',function($row){
                    return '<a href="/piracy/ticket/'.$row->original_ticket_id.'">'.$row->original_ticket_id.'</a>';
                })->addColumn('action',function($row){
                    return '<button class="btn btn-danger btn-sx btn-icon" data-action="delete" data-type="fqdn" data-item="'.$row->fqdn.'"><i class="fas fa-trash"></i></button>';
                })->escapeColumns('original_ticket_id')->make(true);
        }
    }

    public function download_fqdn(Request $request,$line){
        \App\Http\Controllers\Admin\ActionLogController::log(\Auth::user()->id,\Auth::user()->name,"trying to download ps FQDN $line list");
        switch($line){
            case 'offline':
                $fqdns_piracy = \App\Piracy\FQDNs::select('fqdn')->distinct()->pluck('fqdn')->toArray();
                $content = implode("\n",$fqdns_piracy);
                $headers = [
                    'Content-type' => 'text/plain', 
                    'Content-Disposition' => sprintf('attachment; filename="%s"', "fqdns.txt")
                ];
                \App\Http\Controllers\Admin\ActionLogController::log(\Auth::user()->id,\Auth::user()->name,"succeded to download ps FQDN $line list");
                return \Response::make($content, 200, $headers);
            break;
            case 'online':
                $fqdns_piracy = self::get_all_fqdns_json();
                if($fqdns_piracy !== false){
                    $content = implode("\n",$fqdns_piracy);
                    $headers = [
                        'Content-type' => 'text/plain', 
                        'Content-Disposition' => sprintf('attachment; filename="%s"', "fqdns.txt")
                    ];
                    \App\Http\Controllers\Admin\ActionLogController::log(\Auth::user()->id,\Auth::user()->name,"succeded to download ps FQDN $line list");
                    return \Response::make($content, 200, $headers);
                }else{
                    \App\Http\Controllers\Admin\ActionLogController::log(\Auth::user()->id,\Auth::user()->name,"failed to download ps FQDN $line list (online list not available)");
                    return response('',500);
                }
            break;
            default:
                \App\Http\Controllers\Admin\ActionLogController::log(\Auth::user()->id,\Auth::user()->name,"failed to download ps FQDN $line list (invalid list type)");
                return response('',500);
            break;
        }
    }

    public function datatable_ipv4(Request $request){
        if($request->ajax()){
            $data = \App\Piracy\IPv4s::orderBy("timestamp","desc")->get();
            return Datatables::of($data)
                ->rawColumns(
                    ['ipv4','timestamp']
                )->addColumn('original_ticket_id',function($row){
                    return '<a href="/piracy/ticket/'.$row->original_ticket_id.'">'.$row->original_ticket_id.'</a>';
                })->addColumn('action',function($row){
                    return '<button class="btn btn-danger btn-sx btn-icon" data-action="delete" data-type="ipv4" data-item="'.$row->ipv4.'"><i class="fas fa-trash"></i></button>';
                })->escapeColumns('original_ticket_id')->make(true);
        }
    }

    public function download_ipv4(Request $request,$line){
        \App\Http\Controllers\Admin\ActionLogController::log(\Auth::user()->id,\Auth::user()->name,"trying to download ps IPv4 $line list");
        switch($line){
            case 'offline':
                $ipv4s_piracy = \App\Piracy\IPv4s::select('ipv4')->distinct()->pluck('ipv4')->toArray();
                $content = implode("\n",$ipv4s_piracy);
                $headers = [
                    'Content-type' => 'text/plain', 
                    'Content-Disposition' => sprintf('attachment; filename="%s"', "ipv4s.txt")
                ];
                \App\Http\Controllers\Admin\ActionLogController::log(\Auth::user()->id,\Auth::user()->name,"succeded to download ps IPv4 $line list");
                return \Response::make($content, 200, $headers);
            break;
            case 'online':
                $ipv4s_piracy = self::get_all_ipv4s_json();
                if($ipv4s_piracy !== false){
                    $content = implode("\n",$ipv4s_piracy);
                    $headers = [
                        'Content-type' => 'text/plain', 
                        'Content-Disposition' => sprintf('attachment; filename="%s"', "ipv4s.txt")
                    ];
                    \App\Http\Controllers\Admin\ActionLogController::log(\Auth::user()->id,\Auth::user()->name,"succeded to download ps IPv4 $line list");
                    return \Response::make($content, 200, $headers);
                }else{
                    \App\Http\Controllers\Admin\ActionLogController::log(\Auth::user()->id,\Auth::user()->name,"failed to download ps IPv4 $line list (online list not available)");
                    return response('',500);
                }
            break;
            default:
                \App\Http\Controllers\Admin\ActionLogController::log(\Auth::user()->id,\Auth::user()->name,"failed to download ps IPv4 $line list (invalid list type)");
                return response('',500);
            break;
        }
    }

    public function datatable_ipv6(Request $request){
        if($request->ajax()){
            $data = \App\Piracy\IPv6s::orderBy("timestamp","desc")->get();
            return Datatables::of($data)
                ->rawColumns(
                    ['ipv6','timestamp']
                )->addColumn('original_ticket_id',function($row){
                    return '<a href="/piracy/ticket/'.$row->original_ticket_id.'">'.$row->original_ticket_id.'</a>';
                })->addColumn('action',function($row){
                    return '<button class="btn btn-danger btn-sx btn-icon" data-action="delete" data-type="ipv6" data-item="'.$row->ipv6.'"><i class="fas fa-trash"></i></button>';
                })->escapeColumns('original_ticket_id')->make(true);
        }
    }

    public function download_ipv6(Request $request,$line){
        \App\Http\Controllers\Admin\ActionLogController::log(\Auth::user()->id,\Auth::user()->name,"trying to download ps IPv6 $line list");
        switch($line){
            case 'offline':
                $ipv6s_piracy = \App\Piracy\IPv6s::select('ipv6')->distinct()->pluck('ipv6')->toArray();
                $content = implode("\n",$ipv6s_piracy);
                $headers = [
                    'Content-type' => 'text/plain', 
                    'Content-Disposition' => sprintf('attachment; filename="%s"', "ipv6s.txt")
                ];
                \App\Http\Controllers\Admin\ActionLogController::log(\Auth::user()->id,\Auth::user()->name,"succeded to download ps IPv6 $line list");
                return \Response::make($content, 200, $headers);
            break;
            case 'online':
                $ipv6s_piracy = self::get_all_ipv6s_json();
                if($ipv6s_piracy !== false){
                    $content = implode("\n",$ipv6s_piracy);
                    $headers = [
                        'Content-type' => 'text/plain', 
                        'Content-Disposition' => sprintf('attachment; filename="%s"', "ipv6s.txt")
                    ];
                    \App\Http\Controllers\Admin\ActionLogController::log(\Auth::user()->id,\Auth::user()->name,"succeded to download ps IPv6 $line list");
                    return \Response::make($content, 200, $headers);
                }else{
                    \App\Http\Controllers\Admin\ActionLogController::log(\Auth::user()->id,\Auth::user()->name,"failed to download ps IPv6 $line list (online list not available)");
                    return response('',500);
                }
            break;
            default:
                \App\Http\Controllers\Admin\ActionLogController::log(\Auth::user()->id,\Auth::user()->name,"failed to download ps IPv6 $line list (invalid list type)");
                return response('',500);
            break;
        }
    }

    public function crud(Request $request,$type,$action){
        if($request->filled(["item"])){
            $item = $request->input("item");
            switch($action){
                case 'delete':
                    \App\Http\Controllers\Admin\ActionLogController::log(\Auth::user()->id,\Auth::user()->name,"trynig to $action from $type list item $item");
                    switch($type){
                        case 'fqdn':
                            $item = \App\Piracy\FQDNs::find($item);
                        break;
                        case 'ipv4':
                            $item = \App\Piracy\IPv4s::find($item);
                        break;
                        case 'ipv6':
                            $item = \App\Piracy\IPv6s::find($item);
                        break;
                        default:
                            \App\Http\Controllers\Admin\ActionLogController::log(\Auth::user()->id,\Auth::user()->name,"CRUD type not supported (tried to $action from $type list item $item)");
                            return response('',500);
                        break;
                    }
                    if($item){
                        if($item->delete()){
                            \App\Http\Controllers\Admin\ActionLogController::log(\Auth::user()->id,\Auth::user()->name,"succeded to $action from $type list item $item");
                            return response('',200);
                        }else{
                            \App\Http\Controllers\Admin\ActionLogController::log(\Auth::user()->id,\Auth::user()->name,"failed to $action from $type list item $item");
                            return response('',500);
                        }
                    }
                    \App\Http\Controllers\Admin\ActionLogController::log(\Auth::user()->id,\Auth::user()->name,"$type item $item not found (tried to $action from $type list item $item)");
                    return response('',404);
                break;
                default:
                    \App\Http\Controllers\Admin\ActionLogController::log(\Auth::user()->id,\Auth::user()->name,"CRUD action not supported (tried to $action from $type list item $item)");
                    return response('',500);
                break;
            }
        }
        return response('',500);
    }

    public function datatable_whitelist(Request $request){
        if($request->ajax()){
            $data = self::get_whitelist();
            return Datatables::of($data)->make(true);
        }
    }

    private static function get_access_token($force_new_one = false){
        if($force_new_one){
            //if forced new token check if last refresh token is valid
            $last_refresh_token = \App\Piracy\APIRefreshTokens::where('timestamp','>',now()->subWeek())->orderBy('id','desc')->get()->first();
            if($last_refresh_token){
                //if last refresh token is still valid
                $new_access_token = self::refresh_login($last_refresh_token->refresh_token);
                if($new_access_token){
                    //if successfully refreshed
                    $new_access_token_db = new \App\Piracy\APIAccessTokens();
                    $new_access_token_db->access_token = $new_access_token->access_token;
                    $new_access_token_db->save();
                    return $new_access_token_db->access_token;
                }else{
                    //if refresh failed do a new login
                    $new_access_token = self::new_login();
                    if($new_access_token){
                        //if new login success
                        $new_access_token_db = new \App\Piracy\APIAccessTokens();
                        $new_access_token_db->access_token = $new_access_token->access_token;
                        $new_access_token_db->save();
                        $new_refresh_token_db = new \App\Piracy\APIRefreshTokens();
                        $new_refresh_token_db->refresh_token = $new_access_token->refresh_token;
                        $new_refresh_token_db->save();
                        return $new_access_token_db->access_token;
                    }else{
                        //if new login failed return false
                        return false;
                    }
                }
            }else{
                //if last refresh token is expired do a new login
                $new_access_token = self::new_login();
                if($new_access_token){
                    //if new login success
                    $new_access_token_db = new \App\Piracy\APIAccessTokens();
                    $new_access_token_db->access_token = $new_access_token->access_token;
                    $new_access_token_db->save();
                    $new_refresh_token_db = new \App\Piracy\APIRefreshTokens();
                    $new_refresh_token_db->refresh_token = $new_access_token->refresh_token;
                    $new_refresh_token_db->save();
                    return $new_access_token_db->access_token;
                }else{
                    //if new login failed return false
                    return false;
                }
            }
        }else{
            //if not forced new token check if last access token is valid
            $last_access_token = \App\Piracy\APIAccessTokens::where('timestamp','>',now()->subHours(1))->orderBy('id','desc')->get()->first();
            if($last_access_token){
                //if last access token is still valid return it
                return $last_access_token->access_token;
            }else{
                //if last access token is expired check if last refresh token is still valid
                $last_refresh_token = \App\Piracy\APIRefreshTokens::where('timestamp','>',now()->subWeek())->orderBy('id','desc')->get()->first();
                if($last_refresh_token){
                    //if last refresh token is still valid
                    $new_access_token = self::refresh_login($last_refresh_token->refresh_token);
                    if($new_access_token){
                        //if successfully refreshed
                        $new_access_token_db = new \App\Piracy\APIAccessTokens();
                        $new_access_token_db->access_token = $new_access_token->access_token;
                        $new_access_token_db->save();
                        return $new_access_token_db->access_token;
                    }else{
                        //if refresh failed do a new login
                        $new_access_token = self::new_login();
                        if($new_access_token){
                            //if new login success
                            $new_access_token_db = new \App\Piracy\APIAccessTokens();
                            $new_access_token_db->access_token = $new_access_token->access_token;
                            $new_access_token_db->save();
                            $new_refresh_token_db = new \App\Piracy\APIRefreshTokens();
                            $new_refresh_token_db->refresh_token = $new_access_token->refresh_token;
                            $new_refresh_token_db->save();
                            return $new_access_token_db->access_token;
                        }else{
                            //if new login failed return false
                            return false;
                        }
                    }
                }else{
                    //if last refresh token is expired do a new login
                    $new_access_token = self::new_login();
                    if($new_access_token){
                        //if new login success
                        $new_access_token_db = new \App\Piracy\APIAccessTokens();
                        $new_access_token_db->access_token = $new_access_token->access_token;
                        $new_access_token_db->save();
                        $new_refresh_token_db = new \App\Piracy\APIRefreshTokens();
                        $new_refresh_token_db->refresh_token = $new_access_token->refresh_token;
                        $new_refresh_token_db->save();
                        return $new_access_token_db->access_token;
                    }else{
                        //if new login failed return false
                        return false;
                    }
                }
            }
        }
    }

    private static function new_login(){
        \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","trying to authenticate");
        $endpoint = self::buildUrl("/api/v1/authentication/login");
        $body_request = new \StdClass();
        $body_request->email = env("PIRACY_SHIELD_MAIL");
        $body_request->password = env("PIRACY_SHIELD_PSW");
        $client = new \GuzzleHttp\Client();
        try{
            $response = $client->post($endpoint,["json" => $body_request,'connect_timeout' => 5]);
            if($response->getBody()){
                $result = trim($response->getBody()->getContents());
                if(self::isJson($result)){
                    $obj = json_decode($result);
                    if(property_exists($obj,"status")){
                        if($obj->status == "success"){
                            if(property_exists($obj,"data")){
                                if(property_exists($obj->data,"access_token")){
                                    \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","succeded to authenticate");
                                    self::api_log("POST","/api/v1/authentication/login",null,json_encode($body_request),$response->getStatusCode(),$result);
                                    return $obj->data;
                                }else{
                                    \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to authenticate (no access_token property)");
                                }
                            }else{
                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to authenticate (no data property)");
                            }
                        }else{
                            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to authenticate (status: ".$obj->status.")");
                        }
                    }else{
                        \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to authenticate (malformed JSON body)");
                    }
                }else{
                    \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to authenticate (not JSON body)");
                }
            }else{
                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to authenticate (no body in response)");
            }
            return false;
        }catch(\GuzzleHttp\Exception\RequestException $e){
            if($e->hasResponse()){
                if($e->getResponse()->getBody()){
                    $result = trim($e->getResponse()->getBody()->getContents());
                    self::api_log("POST","/api/v1/authentication/login",null,json_encode($body_request),$e->getResponse()->getStatusCode(),$result);
                }
                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to authenticate (".$e->getResponse().")");
                return false;
            }else{
                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to authenticate (connection error)");
                return false;
            }
        }
    }

    private static function refresh_login($token){
        \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","trying to refresh token $token");
        $endpoint = self::buildUrl("/api/v1/authentication/refresh");
        $body_request = new \StdClass();
        $body_request->refresh_token = $token;
        $client = new \GuzzleHttp\Client();
        try{
            $response = $client->post($endpoint,["json" => $body_request,'connect_timeout' => 5]);
            if($response->getBody()){
                $result = trim($response->getBody()->getContents());
                if(self::isJson($result)){
                    $obj = json_decode($result);
                    if(property_exists($obj,"status")){
                        if($obj->status == "success"){
                            if(property_exists($obj,"data")){
                                if(property_exists($obj->data,"access_token")){
                                    \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","succeded to refresh");
                                    self::api_log("POST","/api/v1/authentication/refresh",null,json_encode($body_request),$response->getStatusCode(),$result);
                                    return $obj->data;
                                }else{
                                    \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to refresh (no access_token property)");
                                }
                            }else{
                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to refresh (no data property)");
                            }
                        }else{
                            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to refresh (status: ".$obj->status.")");
                        }
                    }else{
                        \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to refresh (malformed JSON body)");
                    }
                }else{
                    \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to refresh (not JSON body)");
                }
            }else{
                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to refresh (no body in response)");
            }
            return false;
        }catch(\GuzzleHttp\Exception\RequestException $e){
            if($e->hasResponse()){
                if($e->getResponse()->getBody()){
                    $result = trim($e->getResponse()->getBody()->getContents());
                    self::api_log("POST","/api/v1/authentication/refresh",null,json_encode($body_request),$e->getResponse()->getStatusCode(),$result);
                }
                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to refresh (".$e->getResponse().")");
                return false;
            }else{
                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to refresh (connection error)");
                return false;
            }
        }
    }

    private static function logout(){
        \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","trying to logout");
        $endpoint = self::buildUrl("/api/v1/authentication/logout");
        $access_token = self::get_access_token(false);
        if($access_token){
            $client = new \GuzzleHttp\Client();
            try{
                $response = $client->get($endpoint,['headers' => ['Authorization' => "Bearer $access_token"],'connect_timeout' => 5]);
                if($response->getBody()){
                    $result = trim($response->getBody()->getContents());
                    if(self::isJson($result)){
                        $obj = json_decode($result);
                        if(property_exists($obj,"status")){
                            if($obj->status == "success"){
                                if(property_exists($obj,"data")){
                                    \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","succeded to logout");
                                    self::api_log("GET","/api/v1/authentication/logout",$access_token,null,$response->getStatusCode(),$result);
                                    return $obj->data;
                                }else{
                                    \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to logout (no data property)");
                                }
                            }else{
                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to logout (status: ".$obj->status.")");
                            }
                        }else{
                            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to logout (malformed JSON body)");
                        }
                    }else{
                        \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to logout (not JSON body)");
                    }
                }else{
                    \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to logout (no body in response)");
                }
                return false;
            }catch(\GuzzleHttp\Exception\RequestException $e){
                if($e->hasResponse()){
                    if($e->getResponse()->getBody()){
                        $result = trim($e->getResponse()->getBody()->getContents());
                        self::api_log("GET","/api/v1/authentication/logout",$access_token,null,$e->getResponse()->getStatusCode(),$result);
                    }
                    \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to logout (".$e->getResponse().")");
                    return false;
                }else{
                    \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to logout (connection error)");
                    return false;
                }
            }
        }else{
            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to logout (already unauthenticated)");
            return false;
        }
    }

    private static function get_ticket($ticket_id,$is_retry = false){
        \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","trying to get ticket $ticket_id data");
        $endpoint = self::buildUrl("/api/v1/ticket/get");
        $access_token = self::get_access_token(false);
        if($access_token){
            $client = new \GuzzleHttp\Client();
            $body_request = new \StdClass();
            $body_request->ticket_id = $ticket_id;
            try{
                $response = $client->post($endpoint,["json" => $body_request, 'headers' => ['Authorization' => "Bearer $access_token"], 'connect_timeout' => 5]);
                if($response->getBody()){
                    $result = trim($response->getBody()->getContents());
                    self::api_log("POST","/api/v1/ticket/get",$access_token,json_encode($body_request),$response->getStatusCode(),$result);
                    if(self::isJson($result)){
                        $obj = json_decode($result);
                        if(property_exists($obj,"status")){
                            if($obj->status == "success"){
                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","succeded to get ticket $ticket_id data");
                                return $obj->data;
                            }else{
                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get ticket $ticket_id data (status: ".$obj->status.")");
                            }
                        }else{
                            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get ticket $ticket_id data (malformed JSON body)");
                        }
                    }else{
                        \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get ticket $ticket_id data (not JSON body)");
                    }
                }else{
                    \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get ticket $ticket_id data (no body in response)");
                }
                return false;
            }catch(\GuzzleHttp\Exception\RequestException $e){
                if($e->hasResponse()){
                    if($e->getResponse()->getBody()){
                        $result = trim($e->getResponse()->getBody()->getContents());
                        self::api_log("POST","/api/v1/ticket/get",$access_token,json_encode($body_request),$e->getResponse()->getStatusCode(),$result);
                    }
                    switch($e->getResponse()->getStatusCode()){
                        case 401:
                        case 403:
                            if(!$is_retry){
                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","access token is expired, getting new one");
                                $access_token = self::get_access_token(true);
                                if($access_token){
                                    return self::get_ticket($ticket_id,true);
                                }else{
                                    \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","access token is expired, auth failed");
                                }
                            }else{
                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","access token is expired, not retrying");
                            }
                        break;
                        default:
                            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get ticket $ticket_id data (".$e->getResponse().")");
                            return false;
                        break;
                    }
                }else{
                    \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get ticket $ticket_id data (connection error)");
                    return false;
                }
            }
        }else{
            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get ticket $ticket_id data (unauthenticated)");
            return false;
        }
    }

    private static function get_all_tickets($is_retry = false){
        \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","trying to get all tickets");
        $endpoint = self::buildUrl("/api/v1/ticket/get/all");
        $access_token = self::get_access_token(false);
        if($access_token){
            $client = new \GuzzleHttp\Client();
            try{
                $response = $client->get($endpoint,['headers' => ['Authorization' => "Bearer $access_token", 'Accept-Encoding' => 'gzip'], 'connect_timeout' => 60, 'decode_content' => 'gzip']);
                if($response->getBody()){
                    $result = trim($response->getBody()->getContents());
                    self::api_log("GET","/api/v1/ticket/get/all",$access_token,null,$response->getStatusCode(),$result);
                    if(self::isJson($result)){
                        $obj = json_decode($result);
                        if(property_exists($obj,"status")){
                            if($obj->status == "success"){
                                if(property_exists($obj,"data")){
                                    \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","succeded to get all tickets");
                                    return $obj->data;
                                }else{
                                    \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get all tickets (no data property)");
                                }
                            }else{
                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get all tickets (status: ".$obj->status.")");
                            }
                        }else{
                            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get all tickets (malformed JSON body)");
                        }
                    }else{
                        \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get all tickets (not JSON body)");
                    }
                }else{
                    \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get all tickets (no body in response)");
                }
                return false;
            }catch(\GuzzleHttp\Exception\RequestException $e){
                if($e->hasResponse()){
                    if($e->getResponse()->getBody()){
                        $result = trim($e->getResponse()->getBody()->getContents());
                        self::api_log("GET","/api/v1/ticket/get/all",$access_token,null,$e->getResponse()->getStatusCode(),$result);
                    }
                    switch($e->getResponse()->getStatusCode()){
                        case 401:
                        case 403:
                            if(!$is_retry){
                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","access token is expired, getting new one");
                                $access_token = self::get_access_token(true);
                                if($access_token){
                                    return self::get_all_tickets(true);
                                }else{
                                    \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","access token is expired, auth failed");
                                }
                            }else{
                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","access token is expired, not retrying");
                            }
                        break;
                        default:
                            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get all tickets (".$e->getResponse().")");
                            return false;
                        break;
                    }
                }else{
                    \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get all tickets (connection error)");
                    return false;
                }
            }
        }else{
            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get all tickets (unauthenticated)");
            return false;
        }
    }

    private static function get_fqdns_from_ticket($ticket_id,$is_retry = false){
        \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","trying to get fqdns from ticket $ticket_id");
        $endpoint = self::buildUrl("/api/v1/ticket/get/fqdn");
        $access_token = self::get_access_token(false);
        if($access_token){
            $client = new \GuzzleHttp\Client();
            $body_request = new \StdClass();
            $body_request->ticket_id = $ticket_id;
            try{
                $response = $client->get($endpoint,["json" => $body_request, 'headers' => ['Authorization' => "Bearer $access_token"], 'connect_timeout' => 5]);
                if($response->getBody()){
                    $result = trim($response->getBody()->getContents());
                    self::api_log("GET","/api/v1/ticket/get/fqdn",$access_token,json_encode($body_request),$response->getStatusCode(),$result);
                    if(self::isJson($result)){
                        $obj = json_decode($result);
                        if(property_exists($obj,"status")){
                            if($obj->status == "success"){
                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","succeded to get fqdns from ticket $ticket_id");
                                return $obj->data;
                            }else{
                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get fqdns from ticket $ticket_id (status: ".$obj->status.")");
                            }
                        }else{
                            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get fqdns from ticket $ticket_id (malformed JSON body)");
                        }
                    }else{
                        \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get fqdns from ticket $ticket_id (not JSON body)");
                    }
                }else{
                    \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get fqdns from ticket $ticket_id (no body in response)");
                }
                return false;
            }catch(\GuzzleHttp\Exception\RequestException $e){
                if($e->hasResponse()){
                    if($e->getResponse()->getBody()){
                        $result = trim($e->getResponse()->getBody()->getContents());
                        self::api_log("GET","/api/v1/ticket/get/fqdn",$access_token,json_encode($body_request),$e->getResponse()->getStatusCode(),$result);
                    }
                    switch($e->getResponse()->getStatusCode()){
                        case 401:
                        case 403:
                            if(!$is_retry){
                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","access token is expired, getting new one");
                                $access_token = self::get_access_token(true);
                                if($access_token){
                                    return self::get_fqdns_from_ticket($ticket_id,true);
                                }else{
                                    \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","access token is expired, auth failed");
                                }
                            }else{
                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","access token is expired, not retrying");
                            }
                        break;
                        default:
                            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get fqdns from ticket $ticket_id (".$e->getResponse().")");
                            return false;
                        break;
                    }
                }else{
                    \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get fqdns from ticket $ticket_id (connection error)");
                    return false;
                }
            }
        }else{
            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get fqdns from ticket $ticket_id (unauthenticated)");
            return false;
        }
    }

    private static function get_ipv4s_from_ticket($ticket_id,$is_retry = false){
        \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","trying to get ipv4s from ticket $ticket_id");
        $endpoint = self::buildUrl("/api/v1/ticket/get/ipv4");
        $access_token = self::get_access_token(false);
        if($access_token){
            $client = new \GuzzleHttp\Client();
            $body_request = new \StdClass();
            $body_request->ticket_id = $ticket_id;
            try{
                $response = $client->get($endpoint,["json" => $body_request, 'headers' => ['Authorization' => "Bearer $access_token"],'connect_timeout' => 5]);
                if($response->getBody()){
                    $result = trim($response->getBody()->getContents());
                    self::api_log("GET","/api/v1/ticket/get/ipv4",$access_token,json_encode($body_request),$response->getStatusCode(),$result);
                    if(self::isJson($result)){
                        $obj = json_decode($result);
                        if(property_exists($obj,"status")){
                            if($obj->status == "success"){
                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","succeded to get ipv4s from ticket $ticket_id");
                                return $obj->data;
                            }else{
                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get ipv4s from ticket $ticket_id (status: ".$obj->status.")");
                            }
                        }else{
                            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get ipv4s from ticket $ticket_id (malformed JSON body)");
                        }
                    }else{
                        \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get ipv4s from ticket $ticket_id (not JSON body)");
                    }
                }else{
                    \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get ipv4s from ticket $ticket_id (no body in response)");
                }
                return false;
            }catch(\GuzzleHttp\Exception\RequestException $e){
                if($e->hasResponse()){
                    if($e->getResponse()->getBody()){
                        $result = trim($e->getResponse()->getBody()->getContents());
                        self::api_log("GET","/api/v1/ticket/get/ipv4",$access_token,json_encode($body_request),$e->getResponse()->getStatusCode(),$result);
                    }
                    switch($e->getResponse()->getStatusCode()){
                        case 401:
                        case 403:
                            if(!$is_retry){
                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","access token is expired, getting new one");
                                $access_token = self::get_access_token(true);
                                if($access_token){
                                    return self::get_ipv4s_from_ticket($ticket_id,true);
                                }else{
                                    \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","access token is expired, auth failed");
                                }
                            }else{
                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","access token is expired, not retrying");
                            }
                        break;
                        default:
                            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get ipv4s from ticket $ticket_id (".$e->getResponse().")");
                            return false;
                        break;
                    }
                }else{
                    \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get ipv4s from ticket $ticket_id (connection error)");
                    return false;
                }
            }
        }else{
            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get ipv4s from ticket $ticket_id (unauthenticated)");
            return false;
        }
    }

    private static function get_ipv6s_from_ticket($ticket_id,$is_retry = false){
        \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","trying to get ipv6s from ticket $ticket_id");
        $endpoint = self::buildUrl("/api/v1/ticket/get/ipv6");
        $access_token = self::get_access_token(false);
        if($access_token){
            $client = new \GuzzleHttp\Client();
            $body_request = new \StdClass();
            $body_request->ticket_id = $ticket_id;
            try{
                $response = $client->get($endpoint,["json" => $body_request, 'headers' => ['Authorization' => "Bearer $access_token"],'connect_timeout' => 5]);
                if($response->getBody()){
                    $result = trim($response->getBody()->getContents());
                    self::api_log("GET","/api/v1/ticket/get/ipv6",$access_token,json_encode($body_request),$response->getStatusCode(),$result);
                    if(self::isJson($result)){
                        $obj = json_decode($result);
                        if(property_exists($obj,"status")){
                            if($obj->status == "success"){
                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","succeded to get ipv6s from ticket $ticket_id");
                                return $obj->data;
                            }else{
                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get ipv6s from ticket $ticket_id (status: ".$obj->status.")");
                            }
                        }else{
                            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get ipv6s from ticket $ticket_id (malformed JSON body)");
                        }
                    }else{
                        \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get ipv6s from ticket $ticket_id (not JSON body)");
                    }
                }else{
                    \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get ipv6s from ticket $ticket_id (no body in response)");
                }
                return false;
            }catch(\GuzzleHttp\Exception\RequestException $e){
                if($e->hasResponse()){
                    if($e->getResponse()->getBody()){
                        $result = trim($e->getResponse()->getBody()->getContents());
                        self::api_log("GET","/api/v1/ticket/get/ipv6",$access_token,json_encode($body_request),$e->getResponse()->getStatusCode(),$result);
                    }
                    switch($e->getResponse()->getStatusCode()){
                        case 401:
                        case 403:
                            if(!$is_retry){
                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","access token is expired, getting new one");
                                $access_token = self::get_access_token(true);
                                if($access_token){
                                    return self::get_ipv6s_from_ticket($ticket_id,true);
                                }else{
                                    \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","access token is expired, auth failed");
                                }
                            }else{
                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","access token is expired, not retrying");
                            }
                        break;
                        default:
                            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get ipv6s from ticket $ticket_id (".$e->getResponse().")");
                            return false;
                        break;
                    }
                }else{
                    \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get ipv6s from ticket $ticket_id (connection error)");
                    return false;
                }
            }
        }else{
            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get ipv6s from ticket $ticket_id (unauthenticated)");
            return false;
        }
    }

    private static function get_all_fqdns_json($is_retry = false){
        \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","trying to get FQDN json list");
        $endpoint = self::buildUrl("/api/v1/fqdn/get/all");
        $access_token = self::get_access_token(false);
        if($access_token){
            $client = new \GuzzleHttp\Client();
            try{
                $response = $client->get($endpoint,['headers' => ['Authorization' => "Bearer $access_token"],'connect_timeout' => 10]);
                if($response->getBody()){
                    $result = trim($response->getBody()->getContents());
                    self::api_log("GET","/api/v1/fqdn/get/all",$access_token,null,$response->getStatusCode(),$result);
                    if(self::isJson($result)){
                        $obj = json_decode($result);
                        if(property_exists($obj,"status")){
                            if($obj->status == "success"){
                                if(property_exists($obj,"data")){
                                    \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","succeded to get FQDN json list");
                                    return $obj->data;
                                }else{
                                    \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get FQDN json list (no data property)");
                                }
                            }else{
                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get FQDN json list (status: ".$obj->status.")");
                            }
                        }else{
                            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get FQDN json list (malformed JSON body)");
                        }
                    }else{
                        \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get FQDN json list (not JSON body)");
                    }
                }else{
                    \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get FQDN json list (no body in response)");
                }
                return false;
            }catch(\GuzzleHttp\Exception\RequestException $e){
                if($e->hasResponse()){
                    if($e->getResponse()->getBody()){
                        $result = trim($e->getResponse()->getBody()->getContents());
                        self::api_log("GET","/api/v1/fqdn/get/all",$access_token,null,$e->getResponse()->getStatusCode(),$result);
                    }
                    switch($e->getResponse()->getStatusCode()){
                        case 401:
                        case 403:
                            if(!$is_retry){
                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","access token is expired, getting new one");
                                $access_token = self::get_access_token(true);
                                if($access_token){
                                    return self::get_all_fqdns_json(true);
                                }else{
                                    \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","access token is expired, auth failed");
                                }
                            }else{
                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","access token is expired, not retrying");
                            }
                        break;
                        default:
                            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get FQDN json list (".$e->getResponse().")");
                            return false;
                        break;
                    }
                }else{
                    \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get FQDN json list (connection error)");
                    return false;
                }
            }
        }else{
            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get FQDN json list (unauthenticated)");
            return false;
        }
    }

    private static function get_all_ipv4s_json($is_retry = false){
        \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","trying to get IPv4 json list");
        $endpoint = self::buildUrl("/api/v1/ipv4/get/all");
        $access_token = self::get_access_token(false);
        if($access_token){
            $client = new \GuzzleHttp\Client();
            try{
                $response = $client->get($endpoint,['headers' => ['Authorization' => "Bearer $access_token"],'connect_timeout' => 10]);
                if($response->getBody()){
                    $result = trim($response->getBody()->getContents());
                    self::api_log("GET","/api/v1/ipv4/get/all",$access_token,null,$response->getStatusCode(),$result);
                    if(self::isJson($result)){
                        $obj = json_decode($result);
                        if(property_exists($obj,"status")){
                            if($obj->status == "success"){
                                if(property_exists($obj,"data")){
                                    \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","succeded to get IPv4 json list");
                                    return $obj->data;
                                }else{
                                    \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get IPv4 json list (no data property)");
                                }
                            }else{
                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get IPv4 json list (status: ".$obj->status.")");
                            }
                        }else{
                            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get IPv4 json list (malformed JSON body)");
                        }
                    }else{
                        \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get IPv4 json list (not JSON body)");
                    }
                }else{
                    \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get IPv4 json list (no body in response)");
                }
                return false;
            }catch(\GuzzleHttp\Exception\RequestException $e){
                if($e->hasResponse()){
                    if($e->getResponse()->getBody()){
                        $result = trim($e->getResponse()->getBody()->getContents());
                        self::api_log("GET","/api/v1/ipv4/get/all",$access_token,null,$e->getResponse()->getStatusCode(),$result);
                    }
                    switch($e->getResponse()->getStatusCode()){
                        case 401:
                        case 403:
                            if(!$is_retry){
                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","access token is expired, getting new one");
                                $access_token = self::get_access_token(true);
                                if($access_token){
                                    return self::get_all_ipv4s_json(true);
                                }else{
                                    \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","access token is expired, auth failed");
                                }
                            }else{
                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","access token is expired, not retrying");
                            }
                        break;
                        default:
                            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get IPv4 json list (".$e->getResponse().")");
                            return false;
                        break;
                    }
                }else{
                    \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get IPv4 json list (connection error)");
                    return false;
                }
            }
        }else{
            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get IPv4 json list (unauthenticated)");
            return false;
        }
    }

    private static function get_all_ipv6s_json($is_retry = false){
        \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","trying to get IPv6 json list");
        $endpoint = self::buildUrl("/api/v1/ipv6/get/all");
        $access_token = self::get_access_token(false);
        if($access_token){
            $client = new \GuzzleHttp\Client();
            try{
                $response = $client->get($endpoint,['headers' => ['Authorization' => "Bearer $access_token"],'connect_timeout' => 10]);
                if($response->getBody()){
                    $result = trim($response->getBody()->getContents());
                    self::api_log("GET","/api/v1/ipv6/get/all",$access_token,null,$response->getStatusCode(),$result);
                    if(self::isJson($result)){
                        $obj = json_decode($result);
                        if(property_exists($obj,"status")){
                            if($obj->status == "success"){
                                if(property_exists($obj,"data")){
                                    \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","succeded to get ipv6 json list");
                                    return $obj->data;
                                }else{
                                    \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get ipv6 json list (no data property)");
                                }
                            }else{
                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get ipv6 json list (status: ".$obj->status.")");
                            }
                        }else{
                            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get ipv6 json list (malformed JSON body)");
                        }
                    }else{
                        \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get ipv6 json list (not JSON body)");
                    }
                }else{
                    \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get ipv6 json list (no body in response)");
                }
                return false;
            }catch(\GuzzleHttp\Exception\RequestException $e){
                if($e->hasResponse()){
                    if($e->getResponse()->getBody()){
                        $result = trim($e->getResponse()->getBody()->getContents());
                        self::api_log("GET","/api/v1/ipv6/get/all",$access_token,null,$e->getResponse()->getStatusCode(),$result);
                    }
                    switch($e->getResponse()->getStatusCode()){
                        case 401:
                        case 403:
                            if(!$is_retry){
                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","access token is expired, getting new one");
                                $access_token = self::get_access_token(true);
                                if($access_token){
                                    return self::get_all_ipv6s_json(true);
                                }else{
                                    \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","access token is expired, auth failed");
                                }
                            }else{
                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","access token is expired, not retrying");
                            }
                        break;
                        default:
                            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get ipv6 json list (".$e->getResponse().")");
                            return false;
                        break;
                    }
                }else{
                    \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get ipv6 json list (connection error)");
                    return false;
                }
            }
        }else{
            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get ipv6 json list (unauthenticated)");
            return false;
        }
    }

    private static function set_item_processed($ticket_item,$is_retry = false){
        \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","trying to set $ticket_item processed");
        $endpoint = self::buildUrl("/api/v1/ticket/item/set/processed");
        $access_token = self::get_access_token(false);
        if($access_token){
            $client = new \GuzzleHttp\Client();
            $body_request = new \StdClass();
            $body_request->value = $ticket_item;
            try{
                $response = $client->post($endpoint,["json" => $body_request, 'headers' => ['Authorization' => "Bearer $access_token"],'connect_timeout' => 5]);
                if($response->getBody()){
                    $result = trim($response->getBody()->getContents());
                    self::api_log("POST","/api/v1/ticket/item/set/processed",$access_token,json_encode($body_request),$response->getStatusCode(),$result);
                    if(self::isJson($result)){
                        $obj = json_decode($result);
                        if(property_exists($obj,"status")){
                            if($obj->status == "success"){
                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","succeded to set $ticket_item processed");
                                return true;
                            }else{
                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to set $ticket_item processed (status: ".$obj->status.")");
                            }
                        }else{
                            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to set $ticket_item processed (malformed JSON body)");
                        }
                    }else{
                        \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to set $ticket_item processed (not JSON body)");
                    }
                }else{
                    \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to set $ticket_item processed (no body in response)");
                }
                return false;
            }catch(\GuzzleHttp\Exception\RequestException $e){
                if($e->hasResponse()){
                    if($e->getResponse()->getBody()){
                        $result = trim($e->getResponse()->getBody()->getContents());
                        self::api_log("POST","/api/v1/ticket/item/set/processed",$access_token,json_encode($body_request),$e->getResponse()->getStatusCode(),$result);
                    }
                    switch($e->getResponse()->getStatusCode()){
                        case 401:
                        case 403:
                            if(!$is_retry){
                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","access token is expired, getting new one");
                                $access_token = self::get_access_token(true);
                                if($access_token){
                                    return self::get_all_ipv4_json($ticket_item,true);
                                }else{
                                    \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","access token is expired, auth failed");
                                }
                            }else{
                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","access token is expired, not retrying");
                            }
                        break;
                        default:
                            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to set $ticket_item processed (".$e->getResponse().")");
                            return false;
                        break;
                    }
                }else{
                    \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to set $ticket_item processed (connection error)");
                    return false;
                }
            }
        }else{
            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to set $ticket_item processed (unauthenticated)");
            return false;
        }
    }

    private static function set_item_unprocessed($ticket_item,$reason,$is_retry = false){
        \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","trying to set $ticket_item unprocessed for reason $reason");
        $endpoint = self::buildUrl("/api/v1/ticket/item/set/unprocessed");
        $access_token = self::get_access_token(false);
        if($access_token){
            $client = new \GuzzleHttp\Client();
            $body_request = new \StdClass();
            $body_request->value = $ticket_item;
            $body_request->reason = $reason;
            try{
                $response = $client->post($endpoint,["json" => $body_request, 'headers' => ['Authorization' => "Bearer $access_token"],'connect_timeout' => 5]);
                if($response->getBody()){
                    $result = trim($response->getBody()->getContents());
                    self::api_log("POST","/api/v1/ticket/item/set/unprocessed",$access_token,json_encode($body_request),$response->getStatusCode(),$result);
                    if(self::isJson($result)){
                        $obj = json_decode($result);
                        if(property_exists($obj,"status")){
                            if($obj->status == "success"){
                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","succeded to set $ticket_item unprocessed for reason $reason");
                                return true;
                            }else{
                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to set $ticket_item unprocessed for reason $reason (status: ".$obj->status.")");
                            }
                        }else{
                            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to set $ticket_item unprocessed for reason $reason (malformed JSON body)");
                        }
                    }else{
                        \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to set $ticket_item unprocessed for reason $reason (not JSON body)");
                    }
                }else{
                    \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to set $ticket_item unprocessed for reason $reason (no body in response)");
                }
                return false;
            }catch(\GuzzleHttp\Exception\RequestException $e){
                if($e->hasResponse()){
                    if($e->getResponse()->getBody()){
                        $result = trim($e->getResponse()->getBody()->getContents());
                        self::api_log("POST","/api/v1/ticket/item/set/unprocessed",$access_token,json_encode($body_request),$e->getResponse()->getStatusCode(),$result);
                    }
                    switch($e->getResponse()->getStatusCode()){
                        case 401:
                        case 403:
                            if(!$is_retry){
                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","access token is expired, getting new one");
                                $access_token = self::get_access_token(true);
                                if($access_token){
                                    return self::get_all_ipv4_json($ticket_item,$reason,true);
                                }else{
                                    \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","access token is expired, auth failed");
                                }
                            }else{
                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","access token is expired, not retrying");
                            }
                        break;
                        default:
                            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to set $ticket_item unprocessed for reason $reason (".$e->getResponse().")");
                            return false;
                        break;
                    }
                }else{
                    \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to set $ticket_item unprocessed for reason $reason (connection error)");
                    return false;
                }
            }
        }else{
            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to set $ticket_item unprocessed for reason $reason (unauthenticated)");
            return false;
        }
    }

    private static function get_whitelist($is_retry = false){
        \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","trying to get whitelist");
        $endpoint = self::buildUrl("/api/v1/whitelist/item/get/all");
        $access_token = self::get_access_token(false);
        if($access_token){
            $client = new \GuzzleHttp\Client();
            try{
                $response = $client->get($endpoint,['headers' => ['Authorization' => "Bearer $access_token"],'connect_timeout' => 5]);
                if($response->getBody()){
                    $result = trim($response->getBody()->getContents());
                    self::api_log("GET","/api/v1/whitelist/item/get/all",$access_token,null,$response->getStatusCode(),$result);
                    if(self::isJson($result)){
                        $obj = json_decode($result);
                        if(property_exists($obj,"status")){
                            if($obj->status == "success"){
                                if(property_exists($obj,"data")){
                                    \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","succeded to get whitelist");
                                    return $obj->data;
                                }else{
                                    \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get whitelist (no data property)");
                                }
                            }else{
                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get whitelist (status: ".$obj->status.")");
                            }
                        }else{
                            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get whitelist (malformed JSON body)");
                        }
                    }else{
                        \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get whitelist (not JSON body)");
                    }
                }else{
                    \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get whitelist (no body in response)");
                }
                return false;
            }catch(\GuzzleHttp\Exception\RequestException $e){
                if($e->hasResponse()){
                    if($e->getResponse()->getBody()){
                        $result = trim($e->getResponse()->getBody()->getContents());
                        self::api_log("GET","/api/v1/whitelist/item/get/all",$access_token,null,$e->getResponse()->getStatusCode(),$result);
                    }
                    switch($e->getResponse()->getStatusCode()){
                        case 401:
                        case 403:
                            if(!$is_retry){
                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","access token is expired, getting new one");
                                $access_token = self::get_access_token(true);
                                if($access_token){
                                    return self::get_all_tickets(true);
                                }else{
                                    \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","access token is expired, auth failed");
                                }
                            }else{
                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","access token is expired, not retrying");
                            }
                        break;
                        default:
                            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get whitelist (".$e->getResponse().")");
                            return false;
                        break;
                    }
                }else{
                    \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get whitelist (connection error)");
                    return false;
                }
            }
        }else{
            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to get whitelist (unauthenticated)");
            return false;
        }
    }

    private static function delete_whitelist($genre,$item,$is_retry = false){
        \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","trying to delete from whitelist item $item (genre $genre)");
        $endpoint = self::buildUrl("/api/v1/whitelist/item/remove");
        $access_token = self::get_access_token(false);
        if($access_token){
            $client = new \GuzzleHttp\Client();
            $body_request = new \StdClass();
            $body_request->item = $item;
            try{
                $response = $client->post($endpoint,["json" => $body_request, 'headers' => ['Authorization' => "Bearer $access_token"], 'connect_timeout' => 5]);
                if($response->getBody()){
                    $result = trim($response->getBody()->getContents());
                    self::api_log("POST","/api/v1/whitelist/item/remove",$access_token,json_encode($body_request),$response->getStatusCode(),$result);
                    if(self::isJson($result)){
                        $obj = json_decode($result);
                        if(property_exists($obj,"status")){
                            if($obj->status == "success"){
                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","succeded to delete from whitelist item $item (genre $genre)");
                                return true;
                            }else{
                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to delete from whitelist item $item (genre $genre) (status: ".$obj->status.")");
                            }
                        }else{
                            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to delete from whitelist item $item (genre $genre) (malformed JSON body)");
                        }
                    }else{
                        \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to delete from whitelist item $item (genre $genre) (not JSON body)");
                    }
                }else{
                    \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to delete from whitelist item $item (genre $genre) (no body in response)");
                }
                return false;
            }catch(\GuzzleHttp\Exception\RequestException $e){
                if($e->hasResponse()){
                    if($e->getResponse()->getBody()){
                        $result = trim($e->getResponse()->getBody()->getContents());
                        self::api_log("POST","/api/v1/whitelist/item/remove",$access_token,json_encode($body_request),$e->getResponse()->getStatusCode(),$result);
                    }
                    switch($e->getResponse()->getStatusCode()){
                        case 401:
                        case 403:
                            if(!$is_retry){
                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","access token is expired, getting new one");
                                $access_token = self::get_access_token(true);
                                if($access_token){
                                    return self::delete_whitelist($genre,$item,true);
                                }else{
                                    \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","access token is expired, auth failed");
                                }
                            }else{
                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","access token is expired, not retrying");
                            }
                        break;
                        default:
                            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to delete from whitelist item $item (genre $genre) (".$e->getResponse().")");
                            return false;
                        break;
                    }
                }else{
                    \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to delete from whitelist item $item (genre $genre) (connection error)");
                    return false;
                }
            }
        }else{
            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to delete from whitelist item $item (genre $genre) (unauthenticated)");
            return false;
        }
    }

    private static function add_whitelist($genre,$item,$attr_name,$attr,$is_retry = false){
        \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","trying to add to whitelist item $item (genre $genre - $attr_name $attr)");
        $endpoint = self::buildUrl("/api/v1/whitelist/item/create");
        $access_token = self::get_access_token(false);
        if($access_token){
            $client = new \GuzzleHttp\Client();
            $body_request = new \StdClass();
            $body_request->genre = $genre;
            $body_request->item = $item;
            $body_request->$attr_name = $attr;
            try{
                $response = $client->post($endpoint,["json" => $body_request, 'headers' => ['Authorization' => "Bearer $access_token"], 'connect_timeout' => 5]);
                if($response->getBody()){
                    $result = trim($response->getBody()->getContents());
                    self::api_log("POST","/api/v1/whitelist/item/create",$access_token,json_encode($body_request),$response->getStatusCode(),$result);
                    if(self::isJson($result)){
                        $obj = json_decode($result);
                        if(property_exists($obj,"status")){
                            if($obj->status == "success"){
                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","succeded to add to whitelist item $item (genre $genre - $attr_name $attr)");
                                return true;
                            }else{
                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to add to whitelist item $item (genre $genre - $attr_name $attr) (status: ".$obj->status.")");
                            }
                        }else{
                            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to add to whitelist item $item (genre $genre - $attr_name $attr) (malformed JSON body)");
                        }
                    }else{
                        \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to add to whitelist item $item (genre $genre - $attr_name $attr) (not JSON body)");
                    }
                }else{
                    \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to add to whitelist item $item (genre $genre - $attr_name $attr) (no body in response)");
                }
                return false;
            }catch(\GuzzleHttp\Exception\RequestException $e){
                if($e->hasResponse()){
                    if($e->getResponse()->getBody()){
                        $result = trim($e->getResponse()->getBody()->getContents());
                        self::api_log("POST","/api/v1/whitelist/item/create",$access_token,json_encode($body_request),$e->getResponse()->getStatusCode(),$result);
                    }
                    switch($e->getResponse()->getStatusCode()){
                        case 401:
                        case 403:
                            if(!$is_retry){
                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","access token is expired, getting new one");
                                $access_token = self::get_access_token(true);
                                if($access_token){
                                    return self::add_whitelist($genre,$item,$attr_name,$attr,true);
                                }else{
                                    \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","access token is expired, auth failed");
                                }
                            }else{
                                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","access token is expired, not retrying");
                            }
                        break;
                        default:
                            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to add to whitelist item $item (genre $genre - $attr_name $attr) (".$e->getResponse().")");
                            return false;
                        break;
                    }
                }else{
                    \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to add to whitelist item $item (genre $genre - $attr_name $attr) (connection error)");
                    return false;
                }
            }
        }else{
            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to add to whitelist item $item (genre $genre - $attr_name $attr) (unauthenticated)");
            return false;
        }
    }

    public static function ping(){
        \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","trying to ping system");
        $endpoint = self::buildUrl("/api/v1/ping");
        $client = new \GuzzleHttp\Client();
        try{
            $response = $client->get($endpoint,['connect_timeout' => 5]);
            if($response->getBody()){
                $result = trim($response->getBody()->getContents());
                self::api_log("GET","/api/v1/ping",null,null,$response->getStatusCode(),$result);
                if(self::isJson($result)){
                    $obj = json_decode($result);
                    if(property_exists($obj,"status")){
                        if($obj->status == "success"){
                            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","ping result status: ".$obj->status);
                            return true;
                        }else{
                            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","ping result status: ".$obj->status);
                        }
                    }else{
                        \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","ping result: malformed JSON body");
                    }
                }else{
                    \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","ping result: not JSON body");
                }
            }else{
                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","ping result: no body in response");
            }
        }catch(\GuzzleHttp\Exception\RequestException $e){
            if($e->hasResponse()){
                if($e->getResponse()->getBody()){
                    $result = trim($e->getResponse()->getBody()->getContents());
                    self::api_log("GET","/api/v1/ping",null,null,$e->getResponse()->getStatusCode(),$result);
                }
                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","ping result: system is offline (".$e->getResponse().")");
                return false;    
            }else{
                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","ping result: system is offline (connection error)");
                return false;
            }
        }
    }

    private static function buildUrl($component){
        return env('PIRACY_SHIELD_API_URL').$component;
    }

    private static function isJson($string) {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    private static function api_log($method,$endpoint,$token,$body,$code,$answer){
        $log = new \App\Piracy\APILog();
        $log->method = $method;
        $log->endpoint = $endpoint;
        $log->token = $token;
        $log->body = $body;
        $log->code = $code;
        $log->answer = $answer;
        $log->save();
    }

    private static function check_env(){
        $errors = [];
        if(!env('PIRACY_SHIELD_MAIL')){
            $errors[] = "Mail not filled";
        }
        if(!env('PIRACY_SHIELD_PSW')){
            $errors[] = "Password not filled";
        }
        if(!env('PIRACY_SHIELD_API_URL')){
            $errors[] = "API base URL not filled";
        }else{
            if(!filter_var(env('PIRACY_SHIELD_API_URL'), FILTER_VALIDATE_URL)){
                $errors[] = "API base URL not valid";
            }
        }
        if(!env('PIRACY_SHIELD_DNS_REDIRECT_IP')){
            $errors[] = "DNS redirect IP not filled";
        }else{
            if(!filter_var(env('PIRACY_SHIELD_DNS_REDIRECT_IP'), FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)){
                $errors[] = "DNS redirect IP not valid";
            }
        }
        if(!env('PIRACY_SHIELD_VPN_PEER_IP')){
            $errors[] = "VPN peer IP not filled";
        }else{
            if(!filter_var(env('PIRACY_SHIELD_VPN_PEER_IP'), FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)){
                $errors[] = "VPN peer IP not valid";
            }
        }
        if(!env('PIRACY_SHIELD_VPN_REMOTE_LAN_IP')){
            $errors[] = "VPN remote LAN IP not filled";
        }else{
            if(!filter_var(env('PIRACY_SHIELD_VPN_REMOTE_LAN_IP'), FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)){
                $errors[] = "VPN remote LAN IP not valid";
            }
        }
        if(!env('PIRACY_SHIELD_VPN_LOCAL_LAN_IP')){
            $errors[] = "VPN local LAN IP not filled";
        }else{
            if(!filter_var(env('PIRACY_SHIELD_VPN_LOCAL_LAN_IP'), FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)){
                $errors[] = "VPN local LAN IP not valid";
            }
        }
        if(!env('PIRACY_SHIELD_VPN_PSK')){
            $errors[] = "VPN pre-shared key not filled";
        }
        return $errors;
    }

    private static function check_dns_resolution($fqdn){
        try {
            $result = dns_get_record($fqdn,DNS_A);
            if(count($result) > 0){
                if(array_key_exists("ip",$result[0])){
                    return $result[0]["ip"];
                }
            }
        } catch (\Throwable $th) {
            return false;
        }
        return false;
    }

    public static function make_piracy_settings_files(){
        \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","trying to make piracy shield settings file in '".base_path('storage/settings/').'vpn.conf'."'");
        $check_env_ps = self::check_env();
        $check_env_network = \App\Http\Controllers\Admin\AdminController::check_env_network();
        $check_env = array_merge($check_env_ps,$check_env_network);
        if(count($check_env) == 0){
            //ipsec_conf.add
            $left = env('NET_IP');
            $left_subnet = env('PIRACY_SHIELD_VPN_LOCAL_LAN_IP')."/32";
            $right = env('PIRACY_SHIELD_VPN_PEER_IP');
            $rightsubnet = env('PIRACY_SHIELD_VPN_REMOTE_LAN_IP')."/32";
            $content = <<<EOD
conn agcom@ps
    type=tunnel
    keyexchange=ikev2
    ike=aes256-sha256-modp1024!
    esp=aes256-sha256!
    keyexchange=ikev2
    ikelifetime=27000s
    auto=start
    authby=psk
    left=$left
    leftsubnet=$left_subnet
    right=$right
    leftauth=psk
    rightauth=psk
    rightsubnet=$rightsubnet
EOD;
            try{
                file_put_contents(base_path('storage/settings/').'ipsec_conf.add',$content);
                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","succeded to make piracy shield vpn ipsec conf file in '".base_path('storage/settings/').'ipsec_conf.add'."'");
            }catch(\Exception $e){
                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to make piracy shield vpn ipsec conf file in '".base_path('storage/settings/').'ipsec_conf.add'."' (".$e->getMessage().")",true);
            }
            //ipsec_secret.add
            $psk = env('PIRACY_SHIELD_VPN_PSK');
            $content = <<<EOD
$right : PSK "$psk"
EOD;
            try{
                file_put_contents(base_path('storage/settings/').'ipsec_secrets.add',$content);
                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","succeded to make piracy shield vpn ipsec secrets file in '".base_path('storage/settings/').'ipsec_secrets.add'."'");
            }catch(\Exception $e){
                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to make piracy shield vpn ipsec secrets file in '".base_path('storage/settings/').'ipsec_secrets.add'."' (".$e->getMessage().")",true);
            }
            //iptables.add
            $source = env('PIRACY_SHIELD_VPN_LOCAL_LAN_IP');
            $content = <<<EOD
iptables -t nat -A POSTROUTING -d $rightsubnet -j SNAT --to-source $source
EOD;
            try{
                file_put_contents(base_path('storage/settings/').'iptables.add',$content);
                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","succeded to make piracy shield vpn ipsec iptables command file in '".base_path('storage/settings/').'iptables.add'."'");
            }catch(\Exception $e){
                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to make piracy shield vpn ipsec iptables command file in '".base_path('storage/settings/').'iptables.add'."' (".$e->getMessage().")",true);
            }
            //hosts.add
            $host_ip = env('PIRACY_SHIELD_VPN_REMOTE_LAN_IP');
            $host_name = parse_url(env('PIRACY_SHIELD_API_URL'), PHP_URL_HOST);
            $content = "$host_ip\t$host_name\n";
            try{
                file_put_contents(base_path('storage/settings/').'hosts.add',$content);
                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","succeded to make piracy shield hosts file in '".base_path('storage/settings/').'hosts.add'."'");
            }catch(\Exception $e){
                \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","failed to make piracy shield hosts file in '".base_path('storage/settings/').'hosts.add'."' (".$e->getMessage().")",true);
            }
        }else{
            \App\Http\Controllers\Admin\ActionLogController::log(0,"piracy_system","piracy shield settings file not made because of: ".implode(", ",$check_env));
        }
        
    }

    private static function is_updatable($timestamp){
        $now = new \DateTime();
        $now->setTimezone(new \DateTimeZone('Europe/Rome'));
        $check = $now->modify('-48 hours');
        $datetime = \DateTime::createFromFormat('Y-m-d\TH:i:s+', $timestamp, new \DateTimeZone('Europe/Rome'));
        return ($datetime > $check);
    }

    private static function is_editable($timestamp){
        $now = new \DateTime();
        $now->setTimezone(new \DateTimeZone('Europe/Rome'));
        $check = $now->modify('-24 hours');
        $datetime = \DateTime::createFromFormat('Y-m-d\TH:i:s+', $timestamp, new \DateTimeZone('Europe/Rome'));
        return ($datetime > $check);
    }
}

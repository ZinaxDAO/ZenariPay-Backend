<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dispute;
use App\Models\Order;
use App\Models\DisputeMessage;

class DisputeController extends Controller
{
    public function dispute(Request $request, Order $order)
    {
        if($request->post()){
            // create a new dispute for the given order
            $dispute = Dispute::create([
                'user_id'       => $request->user()->id,
                'created_by'    => $request->user()->id,
                'transaction_id'=> $request->transaction_id,
                'dispute_type'  => $request->input('dispute_type'),
                'description'   => $request->input('description'),
                'status'        => 'open',
            ]);
            
            // $dispute->save($dispute);
            
            
            $message = DisputeMessage::create([
                'dispute_id' => $dispute->id,
                'user_id'    => $request->user()->id,
                'message'    => $request->description
            ]);
            
            return get_success_response($message);
        }
        
        // else request type is get.
        $where = [
            'user_id' => $request->user()->id
        ];
        $disputes = Dispute::where($where)->orderBy('created_at', 'desc')->paginate(10);
        return get_success_response($disputes);
    }

    public function resolve(Request $request, Dispute $dispute)
    {
        // update the status of the dispute to resolved
        $dispute->status = 'resolved';
        $dispute->save();

        return get_success_response($dispute);
    }

    public function close(Request $request, $dispute)
    {
        // update the status of the dispute to closed
        $dispute = Dispute::find($dispute);
        $dispute->status = 'closed';
        $dispute->save();

        return get_success_response($dispute);
    }

    public function award(Request $request, Dispute $dispute)
    {
        // update the winner of the dispute
        $dispute->winner_id = $request->input('winner_id');
        $dispute->save();

        return get_success_response($dispute);
    }

    public function reply(Request $request, $dispute_id)
    {
        // add a reply to the dispute
        DisputeMessage::create([
            'user_id' => auth()->id(),
            'dispute_id' => $dispute_id,
            'message' => $request->input('reply'),
        ]);

        return get_success_response(["reply" => $request->reply]);
    }
    
    public function chats(Request $request, $dispute_id)
    {
        // get replies to dispute
        $chats = DisputeMessage::where('dispute_id', $dispute_id)->orderBy('created_at', 'desc')->get();
        return get_success_response($chats);
    }
}

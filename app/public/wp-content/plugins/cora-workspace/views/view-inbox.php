<?php
/**
 * Cora Workspace - Workspace Inbox
 * File: views/view-inbox.php
 * Premium, monochromatic high-fidelity interactive unified messenger control.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Sample chats data
$chats = array(
    array(
        'id'       => 'ch_1',
        'name'     => 'Vipul Malhotra',
        'avatar'   => 'V',
        'last_msg' => 'Great, the shoot is confirmed for Friday morning. Thanks!',
        'time'     => '10m ago',
        'unread'   => true,
        'source'   => 'WhatsApp',
        'source_icon' => '<span class="text-emerald-500 font-bold">W</span>'
    ),
    array(
        'id'       => 'ch_2',
        'name'     => 'Aria Realty Group',
        'avatar'   => 'A',
        'last_msg' => 'Can you send over the commercial land term sheet for review?',
        'time'     => '1h ago',
        'unread'   => false,
        'source'   => 'Email',
        'source_icon' => '<span class="text-zinc-500 font-bold">E</span>'
    ),
    array(
        'id'       => 'ch_3',
        'name'     => 'Rhea Kapoor',
        'avatar'   => 'R',
        'last_msg' => 'Which packages do you recommend for family portrait shoots?',
        'time'     => '3h ago',
        'unread'   => true,
        'source'   => 'SMS',
        'source_icon' => '<span class="text-blue-500 font-bold">S</span>'
    ),
    array(
        'id'       => 'ch_4',
        'name'     => 'Rajesh Sharma',
        'avatar'   => 'R',
        'last_msg' => 'Drone camera logs uploaded for CyberCity listing.',
        'time'     => '1d ago',
        'unread'   => false,
        'source'   => 'Slack',
        'source_icon' => '<span class="text-purple-500 font-bold">SL</span>'
    )
);

// Sample message history for Vipul Malhotra
$messages = array(
    array(
        'sender'  => 'Client',
        'content' => 'Hi Cora team, I wanted to double check the call-time for our shoot this Friday.',
        'time'    => '11:05 AM'
    ),
    array(
        'sender'  => 'Cora AI',
        'content' => 'Hello Vipul! The shoot is scheduled from 09:00 AM to 01:00 PM this Friday at the Golf Course Road Villa. Lead broker Rajesh Sharma is assigned to coordinate.',
        'time'    => '11:06 AM'
    ),
    array(
        'sender'  => 'Client',
        'content' => 'Great, the shoot is confirmed for Friday morning. Thanks!',
        'time'    => '11:10 AM'
    )
);
?>

<div class="space-y-6 font-sans text-zinc-900 select-none max-w-[1700px] mx-auto pb-12">
    <!-- Page Header -->
    <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-6 border-b border-zinc-200">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-zinc-950">● Workspace Unified Inbox</h1>
            <p class="text-xs font-medium text-zinc-500 mt-1">Unified client communication dashboard linking WhatsApp, email, SMS, and chat channels.</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="if(window.coraShowToast) window.coraShowToast('Inbox sync updated.', 'success')" class="px-4 py-2 bg-white border border-zinc-200 hover:bg-zinc-50 text-zinc-800 text-xs font-bold rounded-xl transition-all shadow-2xs cursor-pointer flex items-center gap-2">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/></svg>
                Sync Channels
            </button>
        </div>
    </header>

    <!-- Unified Inbox Layout Split Panel -->
    <div class="bg-white border border-zinc-200 rounded-2xl shadow-sm overflow-hidden grid grid-cols-1 md:grid-cols-3 h-[600px]">
        <!-- 1/3: Sidebar Chat List -->
        <div class="border-r border-zinc-150 flex flex-col h-full bg-zinc-50/20">
            <!-- Search & Filters -->
            <div class="p-4 border-b border-zinc-150 space-y-3 bg-white">
                <div class="relative">
                    <input type="text" placeholder="Search conversations..." class="w-full bg-zinc-50 border-0 rounded-xl px-3.5 py-2.5 text-xs font-semibold text-zinc-900 focus:outline-none focus:ring-1 focus:ring-zinc-300">
                </div>
            </div>

            <!-- Conversations List -->
            <div class="flex-1 overflow-y-auto divide-y divide-zinc-100">
                <?php foreach ($chats as $chat) : ?>
                    <div class="p-4 hover:bg-zinc-50 transition-all flex items-start gap-3 cursor-pointer <?php echo ($chat['id'] === 'ch_1') ? 'bg-zinc-100/60' : ''; ?>">
                        <div class="w-9 h-9 rounded-full bg-zinc-800 text-white font-extrabold flex items-center justify-center shrink-0 text-sm relative">
                            <?php echo $chat['avatar']; ?>
                            <?php if ($chat['unread']) : ?>
                                <span class="absolute top-0 right-0 w-2.5 h-2.5 bg-emerald-500 rounded-full border border-white"></span>
                            <?php endif; ?>
                        </div>

                        <div class="flex-1 min-w-0 space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-extrabold text-zinc-900 truncate"><?php echo esc_html($chat['name']); ?></span>
                                <span class="text-[10px] text-zinc-400 font-mono"><?php echo esc_html($chat['time']); ?></span>
                            </div>
                            <p class="text-xs text-zinc-500 truncate leading-normal"><?php echo esc_html($chat['last_msg']); ?></p>
                            <div class="flex items-center gap-1.5 pt-0.5">
                                <span class="px-1.5 py-0.5 bg-white border border-zinc-200 rounded text-[9px] font-bold text-zinc-500 flex items-center gap-1">
                                    <?php echo $chat['source_icon']; ?>
                                    <?php echo esc_html($chat['source']); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- 2/3: Chat Pane & Message History -->
        <div class="md:col-span-2 flex flex-col h-full bg-white">
            <!-- Active Chat Header -->
            <div class="px-6 py-4 border-b border-zinc-150 flex items-center justify-between bg-white">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-zinc-900 text-white font-extrabold flex items-center justify-center text-xs">V</div>
                    <div>
                        <h3 class="text-xs font-bold text-zinc-950">Vipul Malhotra</h3>
                        <p class="text-[10px] text-zinc-400 font-medium">WhatsApp Connected · Active Now</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button class="p-2 hover:bg-zinc-50 rounded-xl border border-zinc-150 transition-all cursor-pointer" onclick="if(window.coraShowToast) window.coraShowToast('Opening lead profile...', 'info')">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </button>
                </div>
            </div>

            <!-- Messages Window -->
            <div class="flex-1 overflow-y-auto p-6 space-y-4 bg-zinc-50/20">
                <?php foreach ($messages as $msg) : 
                    $is_ai = ($msg['sender'] === 'Cora AI');
                    $is_client = ($msg['sender'] === 'Client');
                    
                    $box_class = 'bg-zinc-100 text-zinc-950 mr-auto';
                    if ($is_ai) {
                        $box_class = 'bg-zinc-900 text-white ml-auto';
                    }
                ?>
                    <div class="flex flex-col max-w-[75%] <?php echo ($is_ai) ? 'ml-auto items-end' : 'mr-auto items-start'; ?>">
                        <div class="px-4 py-2.5 rounded-2xl text-xs font-medium leading-relaxed <?php echo $box_class; ?>">
                            <?php echo esc_html($msg['content']); ?>
                        </div>
                        <span class="text-[9px] text-zinc-400 font-mono mt-1 px-1"><?php echo esc_html($msg['time']); ?> · <?php echo esc_html($msg['sender']); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Message Input Area -->
            <div class="p-4 border-t border-zinc-150 bg-white">
                <form onsubmit="event.preventDefault(); if(window.coraShowToast) { window.coraShowToast('Message dispatched.', 'success'); document.getElementById('inbox-reply-input').value=''; }" class="flex gap-2">
                    <input type="text" id="inbox-reply-input" required placeholder="Type your reply (WhatsApp templates enabled)..." class="flex-1 bg-zinc-50 border-0 rounded-xl px-4 py-3 text-xs font-semibold text-zinc-900 focus:outline-none focus:ring-1 focus:ring-zinc-300">
                    <button type="submit" class="px-5 py-3 bg-zinc-950 hover:bg-zinc-900 text-white text-xs font-bold rounded-xl shadow-xs transition-all cursor-pointer">Send Reply</button>
                </form>
            </div>
        </div>
    </div>
</div>

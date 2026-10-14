<?php
include 'cdn.php';
include 'config.php';
$usr_string = $req;

$numbers = preg_replace('/\D/', '', $req);

$chk = $conn->query("select * from user_details where uid='$numbers'");
if($chk->num_rows > 0) {
    while($loop = $chk->fetch_assoc()) {
        $uid = $loop['uid'];

        $bl_check = $conn->query("select * from bal where uid='$uid'");
        if($bl_check->num_rows < 1) {
            $conn->query("insert into bal(uid,bal) values('$uid','0.00')");
            $bl_check = $conn->query("select * from bal where uid='$uid'");
        }
        $itr = $bl_check->fetch_assoc();

        include 'dashboard_header.php';
        ?>

<style>
/* ── Reset viewport zoom on mobile ── */
* { -webkit-text-size-adjust: 100%; text-size-adjust: 100%; }

/* ── Ticker ── */
@keyframes ticker {
    0%   { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}
.ticker-track { animation: ticker 35s linear infinite; }
.ticker-track:hover { animation-play-state: paused; }

/* ── Price flash ── */
@keyframes flashGreen { 0%,100%{background:transparent} 50%{background:rgba(74,222,128,.15)} }
@keyframes flashRed   { 0%,100%{background:transparent} 50%{background:rgba(248,113,113,.15)} }
.flash-green { animation: flashGreen .5s ease; }
.flash-red   { animation: flashRed   .5s ease; }

/* ── Sparkline canvas ── */
.spark { display:block; width:80px; height:32px; }

/* ── Card hover glow ── */
.stat-card { transition: box-shadow .3s, transform .3s; }
.stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 32px rgba(240,165,0,.08); }

/* ── Responsive overrides ── */
@media (max-width: 640px) {
    .dash-pad   { padding: 1rem !important; }
    .hide-sm    { display: none !important; }
    .text-bal   { font-size: 2rem !important; }
    .btn-wrap   { flex-direction: column !important; }
    .btn-wrap a { width: 100% !important; justify-content: center !important; }
    .stats-mini { grid-template-columns: 1fr 1fr !important; }
}
</style>

<!-- ══════════════════════════════════════════
     TOP TICKER BAR
═══════════════════════════════════════════ -->
<div class="mb-6 -mx-4 lg:-mx-10 bg-white/[0.03] border-y border-white/5 py-2.5 overflow-hidden whitespace-nowrap">
    <div class="ticker-track flex gap-10 w-max">
        <?php
        $tickerCoins = [
            ['id'=>'btc',  'label'=>'BTC/USDT', 'icon'=>'fab fa-bitcoin',   'color'=>'text-orange-400'],
            ['id'=>'eth',  'label'=>'ETH/USDT', 'icon'=>'fab fa-ethereum',  'color'=>'text-blue-400'],
            ['id'=>'sol',  'label'=>'SOL/USDT', 'icon'=>'fas fa-bolt',      'color'=>'text-purple-400'],
            ['id'=>'bnb',  'label'=>'BNB/USDT', 'icon'=>'fas fa-coins',     'color'=>'text-yellow-400'],
            ['id'=>'ada',  'label'=>'ADA/USDT', 'icon'=>'fas fa-circle',    'color'=>'text-cyan-400'],
            ['id'=>'xrp',  'label'=>'XRP/USDT', 'icon'=>'fas fa-water',     'color'=>'text-sky-400'],
            ['id'=>'doge', 'label'=>'DOGE/USDT','icon'=>'fas fa-dog',       'color'=>'text-yellow-300'],
            ['id'=>'avax', 'label'=>'AVAX/USDT','icon'=>'fas fa-mountain',  'color'=>'text-red-400'],
        ];
        // Duplicate for seamless loop
        $allTicker = array_merge($tickerCoins, $tickerCoins);
        foreach($allTicker as $c): ?>
        <div class="flex items-center gap-2 px-1">
            <i class="<?php echo $c['icon']; ?> <?php echo $c['color']; ?> text-xs"></i>
            <span class="text-[10px] font-700 text-gray-500 uppercase tracking-widest"><?php echo $c['label']; ?></span>
            <span class="text-[11px] font-700 font-mono text-white" id="tick_<?php echo $c['id']; ?>_val">--</span>
            <span class="text-[10px] font-700 text-green-400" id="tick_<?php echo $c['id']; ?>_chg">--</span>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- ══════════════════════════════════════════
     BALANCE + ACTIONS
═══════════════════════════════════════════ -->
<div class="card-dark rounded-2xl sm:rounded-[2rem] p-5 sm:p-8 lg:p-10 border border-white/5 relative overflow-hidden mb-6 stat-card">
    <div class="absolute top-0 right-0 w-64 h-64 bg-orange-500/5 rounded-full blur-[100px] -mr-32 -mt-32 pointer-events-none"></div>

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-5 relative z-10">
        <div>
            <p class="text-[10px] font-700 text-gray-500 uppercase tracking-[0.2em] mb-2">Total Account Balance</p>
            <div class="flex items-baseline gap-3 flex-wrap">
                <h3 class="text-bal text-3xl sm:text-4xl lg:text-5xl font-syne font-800 text-white">
                    $<?php echo number_format($itr['bal'], 2, '.', ','); ?>
                </h3>
                <span class="text-green-400 font-700 text-sm flex items-center gap-1">
                    <i class="fas fa-caret-up text-xs"></i> +2.4%
                </span>
            </div>
            <p class="text-gray-400 text-xs sm:text-sm mt-2 flex items-center gap-2">
                <i class="fab fa-bitcoin text-orange-500 text-xs"></i>
                <span id="main_btc_bal" class="font-600 tracking-wider font-mono">Loading…</span>
            </p>
        </div>

        <div class="btn-wrap flex flex-wrap gap-3">
            <a href="/web/<?php echo $req; ?>" class="bg-gradient-gold text-black px-5 py-3 rounded-xl font-800 text-sm shadow-lg shadow-orange-500/20 hover:scale-105 transition-all flex items-center gap-2">
                <i class="fas fa-wallet"></i> Connect Web3
            </a>
            <a href="/withdraw/<?php echo $req; ?>" class="bg-white/5 hover:bg-white/10 border border-white/10 px-5 py-3 rounded-xl font-800 text-sm transition-all flex items-center gap-2 text-white">
                <i class="fas fa-arrow-up-from-bracket"></i> Withdraw
            </a>
        </div>
    </div>

    <!-- Mini stats strip -->
    <div class="stats-mini grid grid-cols-2 sm:grid-cols-4 gap-3 mt-6 pt-6 border-t border-white/5">
        <div class="bg-white/[0.02] rounded-xl p-3">
            <p class="text-[9px] text-gray-500 uppercase font-700 tracking-wider mb-1">Weekly P&L</p>
            <p class="text-sm font-700 text-green-400">+$1,402.50</p>
        </div>
        <div class="bg-white/[0.02] rounded-xl p-3">
            <p class="text-[9px] text-gray-500 uppercase font-700 tracking-wider mb-1">Win Rate</p>
            <p class="text-sm font-700 text-primary">78.4%</p>
        </div>
        <div class="bg-white/[0.02] rounded-xl p-3">
            <p class="text-[9px] text-gray-500 uppercase font-700 tracking-wider mb-1">Open Trades</p>
            <p class="text-sm font-700 text-white">0 Active</p>
        </div>
        <div class="bg-white/[0.02] rounded-xl p-3">
            <p class="text-[9px] text-gray-500 uppercase font-700 tracking-wider mb-1">Account Tier</p>
            <p class="text-sm font-700 text-blue-400">Premium</p>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════
     LIVE CRYPTO WIDGETS ROW
═══════════════════════════════════════════ -->
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
    <?php
    $widgets = [
        ['id'=>'btc',  'name'=>'Bitcoin',  'sym'=>'BTC', 'icon'=>'fab fa-bitcoin',  'bg'=>'bg-orange-500/10', 'color'=>'text-orange-400'],
        ['id'=>'eth',  'name'=>'Ethereum', 'sym'=>'ETH', 'icon'=>'fab fa-ethereum', 'bg'=>'bg-blue-500/10',   'color'=>'text-blue-400'],
        ['id'=>'sol',  'name'=>'Solana',   'sym'=>'SOL', 'icon'=>'fas fa-bolt',     'bg'=>'bg-purple-500/10', 'color'=>'text-purple-400'],
        ['id'=>'bnb',  'name'=>'BNB',      'sym'=>'BNB', 'icon'=>'fas fa-coins',    'bg'=>'bg-yellow-500/10', 'color'=>'text-yellow-400'],
    ];
    foreach($widgets as $w): ?>
    <div class="stat-card card-dark rounded-xl sm:rounded-2xl p-4 border border-white/5" id="widget_<?php echo $w['id']; ?>">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg <?php echo $w['bg']; ?> flex items-center justify-center">
                    <i class="<?php echo $w['icon']; ?> <?php echo $w['color']; ?> text-sm"></i>
                </div>
                <div>
                    <p class="text-xs font-700 text-white"><?php echo $w['sym']; ?></p>
                    <p class="text-[9px] text-gray-500 hide-sm"><?php echo $w['name']; ?></p>
                </div>
            </div>
            <span class="text-[9px] font-800 px-1.5 py-0.5 rounded-full bg-green-500/10 text-green-400" id="widget_<?php echo $w['id']; ?>_chg">0.00%</span>
        </div>
        <p class="text-sm sm:text-base font-700 font-mono text-white mb-2" id="widget_<?php echo $w['id']; ?>_price">--</p>
        <canvas class="spark" id="spark_<?php echo $w['id']; ?>"></canvas>
    </div>
    <?php endforeach; ?>
</div>

<!-- ══════════════════════════════════════════
     CHART + MARKETS + TRANSACTIONS
═══════════════════════════════════════════ -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 mb-6">

    <!-- Chart -->
    <div class="lg:col-span-2 card-dark rounded-2xl sm:rounded-[2rem] p-5 sm:p-8 border border-white/5 overflow-hidden stat-card">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
            <div>
                <h4 class="font-syne font-700 text-base sm:text-lg">Market Performance</h4>
                <p class="text-[10px] text-gray-500 mt-0.5">BTC/USDT · Real-time</p>
            </div>
            <div class="flex bg-black/60 p-1 rounded-xl border border-white/10 self-start sm:self-auto">
                <button class="px-3 py-1.5 rounded-lg text-[10px] font-800 bg-white/10 text-white border border-white/5">1H</button>
                <button class="px-3 py-1.5 rounded-lg text-[10px] font-800 text-gray-500 hover:text-white transition">4H</button>
                <button class="px-3 py-1.5 rounded-lg text-[10px] font-800 text-gray-500 hover:text-white transition">1D</button>
                <button class="px-3 py-1.5 rounded-lg text-[10px] font-800 text-gray-500 hover:text-white transition">1W</button>
            </div>
        </div>

        <!-- Live chart canvas -->
        <div class="relative w-full rounded-2xl bg-black/40 border border-white/5 overflow-hidden" style="height:200px; min-height:160px;">
            <canvas id="mainChart" style="width:100%;height:100%;display:block;"></canvas>
            <div class="absolute top-3 left-4">
                <span class="text-[10px] text-gray-500 font-700 uppercase tracking-widest">BTC/USDT</span>
                <p class="text-lg font-700 font-mono text-white mt-0.5" id="chart_price_label">--</p>
            </div>
        </div>
    </div>

    <!-- Live Markets List -->
    <div class="card-dark rounded-2xl sm:rounded-[2rem] p-5 sm:p-8 border border-white/5 stat-card">
        <div class="flex items-center justify-between mb-5">
            <h4 class="font-syne font-700 text-base sm:text-lg">Live Markets</h4>
            <span class="text-[9px] bg-green-500/10 text-green-400 px-2 py-1 rounded-full font-800 uppercase flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-green-400 animate-pulse inline-block"></span> Live
            </span>
        </div>

        <div class="space-y-3">
            <?php
            $markets = [
                ['id'=>'btc',  'sym'=>'BTC', 'name'=>'Bitcoin',  'icon'=>'fab fa-bitcoin',  'bg'=>'bg-orange-500/10', 'c'=>'text-orange-400'],
                ['id'=>'eth',  'sym'=>'ETH', 'name'=>'Ethereum', 'icon'=>'fab fa-ethereum', 'bg'=>'bg-blue-500/10',   'c'=>'text-blue-400'],
                ['id'=>'sol',  'sym'=>'SOL', 'name'=>'Solana',   'icon'=>'fas fa-bolt',     'bg'=>'bg-purple-500/10', 'c'=>'text-purple-400'],
                ['id'=>'bnb',  'sym'=>'BNB', 'name'=>'BNB',      'icon'=>'fas fa-coins',    'bg'=>'bg-yellow-500/10', 'c'=>'text-yellow-400'],
                ['id'=>'xrp',  'sym'=>'XRP', 'name'=>'Ripple',   'icon'=>'fas fa-water',    'bg'=>'bg-sky-500/10',    'c'=>'text-sky-400'],
            ];
            foreach($markets as $m): ?>
            <div class="flex items-center justify-between p-2 rounded-xl hover:bg-white/5 transition-colors cursor-pointer" id="mkt_row_<?php echo $m['id']; ?>">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg <?php echo $m['bg']; ?> flex items-center justify-center flex-shrink-0">
                        <i class="<?php echo $m['icon']; ?> <?php echo $m['c']; ?> text-sm"></i>
                    </div>
                    <div>
                        <p class="text-xs font-700 text-white"><?php echo $m['sym']; ?></p>
                        <p class="text-[9px] text-gray-500"><?php echo $m['name']; ?></p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-xs font-700 text-white font-mono" id="mkt_<?php echo $m['id']; ?>_price">--</p>
                    <p class="text-[9px] font-700 text-green-400" id="mkt_<?php echo $m['id']; ?>_chg">--</p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <button class="w-full mt-5 py-3 bg-white/5 hover:bg-white/10 rounded-xl text-[9px] uppercase tracking-[0.2em] font-800 transition-all text-gray-500 hover:text-white">Full Market Watch</button>
    </div>
</div>

<!-- ══════════════════════════════════════════
     RECENT TRANSACTIONS + SUPPORT
═══════════════════════════════════════════ -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">

    <!-- Transactions -->
    <div class="lg:col-span-2 card-dark rounded-2xl sm:rounded-[2rem] p-5 sm:p-8 border border-white/5 stat-card">
        <div class="flex items-center justify-between mb-5">
            <h4 class="font-syne font-700 text-base sm:text-lg text-white">Recent Activity</h4>
            <a href="/history/<?php echo $req; ?>" class="text-[9px] text-primary font-800 uppercase tracking-wider hover:text-white transition">Full History</a>
        </div>

       <!-- ══════════════════════════════════════════
     RECENT TRANSACTIONS + SUPPORT
═══════════════════════════════════════════ -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">

    <!-- Transactions -->
    <div class="lg:col-span-2 card-dark rounded-2xl sm:rounded-[2rem] p-5 sm:p-8 border border-white/5 stat-card">
        <div class="flex items-center justify-between mb-5">
            <h4 class="font-syne font-700 text-base sm:text-lg text-white">Recent Activity</h4>
            <a href="/history/<?php echo $req; ?>" class="text-[9px] text-primary font-800 uppercase tracking-wider hover:text-white transition">Full History</a>
        </div>

<div class="space-y-2">
<?php
$txn_query = $conn->query("SELECT * FROM withdrawals WHERE uid='$uid' ORDER BY tm DESC LIMIT 5");

if ($txn_query->num_rows > 0):
    while ($txn = $txn_query->fetch_assoc()):
        $isApproved = $txn['stat'] === 'Approved';
        $isDeclined = $txn['stat'] === 'Declined';

        if ($isApproved) {
            $iconClass = 'fas fa-arrow-up-from-bracket';
            $iconBg    = 'bg-green-500/10';
            $iconColor = 'text-green-400';
            $amtColor  = 'text-green-400';
            $amtPrefix = '+';
            $badgeCls  = 'bg-green-500/10 text-green-400';
        } elseif ($isDeclined) {
            $iconClass = 'fas fa-xmark';
            $iconBg    = 'bg-red-500/10';
            $iconColor = 'text-red-400';
            $amtColor  = 'text-red-400';
            $amtPrefix = '-';
            $badgeCls  = 'bg-red-500/10 text-red-400';
        } else {
            $iconClass = 'fas fa-clock';
            $iconBg    = 'bg-yellow-500/10';
            $iconColor = 'text-yellow-400';
            $amtColor  = 'text-yellow-400';
            $amtPrefix = '';
            $badgeCls  = 'bg-yellow-500/10 text-yellow-400';
        }

        $txnTime = strtotime($txn['tm']);
        $diff    = time() - $txnTime;
        if ($diff < 3600)       $timeLabel = floor($diff / 60) . 'm ago';
        elseif ($diff < 86400)  $timeLabel = floor($diff / 3600) . 'h ago';
        elseif ($diff < 172800) $timeLabel = 'Yesterday';
        else                    $timeLabel = date('M d', $txnTime);

        $wallet      = htmlspecialchars($txn['btc_wallet']);
        $walletShort = substr($wallet, 0, 8) . '…' . substr($wallet, -5);
?>
        <div class="p-3 rounded-xl hover:bg-white/5 transition-colors">

            <!-- Row 1: icon + label + amount -->
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl <?php echo $iconBg; ?> flex items-center justify-center <?php echo $iconColor; ?> text-xs flex-shrink-0">
                    <i class="<?php echo $iconClass; ?>"></i>
                </div>

                <div class="flex-1 min-w-0">
                    <p class="text-sm font-700 text-white leading-tight">BTC Withdrawal</p>
                </div>

                <!-- Amount — never wraps, never truncates -->
                <p class="text-sm font-700 <?php echo $amtColor; ?> flex-shrink-0 tabular-nums">
                    <?php echo $amtPrefix . '$' . number_format($txn['amt'], 2); ?>
                </p>
            </div>

            <!-- Row 2: wallet + badge + time — sits under the icon gutter -->
            <div class="flex items-center gap-2 mt-1.5" style="padding-left: 48px;">
                <p class="text-[10px] text-gray-500 font-600 font-mono truncate flex-1 min-w-0">
                    <?php echo $walletShort; ?>
                </p>
                <span class="text-[9px] font-800 px-2 py-0.5 rounded-full flex-shrink-0 <?php echo $badgeCls; ?>">
                    <?php echo htmlspecialchars($txn['stat']); ?>
                </span>
                <span class="text-[9px] text-gray-600 font-700 flex-shrink-0"><?php echo $timeLabel; ?></span>
            </div>

        </div>
<?php
    endwhile;
else:
?>
        <div class="flex flex-col items-center justify-center py-10 text-center">
            <div class="w-12 h-12 rounded-2xl bg-white/5 flex items-center justify-center mb-3">
                <i class="fas fa-inbox text-gray-600 text-lg"></i>
            </div>
            <p class="text-sm font-700 text-gray-500">No transactions yet</p>
            <p class="text-[10px] text-gray-600 mt-1">Your withdrawal history will appear here</p>
        </div>
<?php endif; ?>
</div>
    </div>

    <!-- VIP Support — unchanged -->
    </div>

    <!-- VIP Support -->
    <div class="card-dark rounded-2xl sm:rounded-[2rem] p-5 sm:p-8 border border-white/5 stat-card flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between mb-5">
                <h4 class="font-syne font-700 text-base sm:text-lg text-white">Signal Desk</h4>
                <span class="text-[9px] bg-green-500/10 text-green-400 px-2 py-1 rounded-full font-800 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-400 animate-pulse inline-block"></span> Online
                </span>
            </div>
            <p class="text-[11px] text-gray-400 leading-relaxed mb-6">Personal account managers are currently online and ready to assist with your trades and recovery needs.</p>
        </div>

        <a href="/support/<?php echo $req; ?>-Support" class="w-full bg-gradient-gold text-black py-3.5 rounded-xl font-800 text-sm flex items-center justify-center gap-2 hover:scale-[1.02] transition-all shadow-lg shadow-orange-500/20">
            <i class="fas fa-comments"></i> Open Secure Chat
        </a>
    </div>
</div>

<!-- ══════════════════════════════════════════
     JAVASCRIPT — WebSocket + Charts
═══════════════════════════════════════════ -->
<script>
(function() {
    const userBalUSD = <?php echo (float)$itr['bal']; ?>;

    // ── Sparkline data stores ──
    const sparkData = { btc:[], eth:[], sol:[], bnb:[] };
    const mainChartData = [];
    const MAX_SPARK = 20;
    const MAX_MAIN  = 60;

    // ── Draw sparkline ──
    function drawSpark(id, data, up) {
        const canvas = document.getElementById('spark_' + id);
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        const W = canvas.offsetWidth  || 80;
        const H = canvas.offsetHeight || 32;
        canvas.width  = W;
        canvas.height = H;
        if (data.length < 2) return;

        const min = Math.min(...data);
        const max = Math.max(...data);
        const range = max - min || 1;
        const pad = 3;

        ctx.clearRect(0, 0, W, H);

        const pts = data.map((v, i) => ({
            x: pad + (i / (data.length - 1)) * (W - pad * 2),
            y: H - pad - ((v - min) / range) * (H - pad * 2)
        }));

        // Gradient fill
        const grad = ctx.createLinearGradient(0, 0, 0, H);
        const color = up ? '74,222,128' : '248,113,113';
        grad.addColorStop(0, `rgba(${color},.35)`);
        grad.addColorStop(1, `rgba(${color},0)`);

        ctx.beginPath();
        ctx.moveTo(pts[0].x, pts[0].y);
        pts.slice(1).forEach(p => ctx.lineTo(p.x, p.y));
        ctx.lineTo(pts[pts.length-1].x, H);
        ctx.lineTo(pts[0].x, H);
        ctx.closePath();
        ctx.fillStyle = grad;
        ctx.fill();

        // Line
        ctx.beginPath();
        ctx.moveTo(pts[0].x, pts[0].y);
        pts.slice(1).forEach(p => ctx.lineTo(p.x, p.y));
        ctx.strokeStyle = up ? '#4ade80' : '#f87171';
        ctx.lineWidth   = 1.5;
        ctx.stroke();
    }

    // ── Draw main BTC chart ──
    function drawMain(data) {
        const canvas = document.getElementById('mainChart');
        if (!canvas) return;
        const W = canvas.parentElement.offsetWidth  || 400;
        const H = canvas.parentElement.offsetHeight || 200;
        canvas.width  = W;
        canvas.height = H;
        if (data.length < 2) return;

        const ctx   = canvas.getContext('2d');
        const min   = Math.min(...data);
        const max   = Math.max(...data);
        const range = max - min || 1;
        const pad   = 8;

        ctx.clearRect(0, 0, W, H);

        const pts = data.map((v, i) => ({
            x: pad + (i / (data.length - 1)) * (W - pad * 2),
            y: H - pad - ((v - min) / range) * (H - pad * 2)
        }));

        // Grid lines
        ctx.strokeStyle = 'rgba(255,255,255,0.04)';
        ctx.lineWidth   = 1;
        [0.25, 0.5, 0.75].forEach(frac => {
            const y = pad + frac * (H - pad * 2);
            ctx.beginPath(); ctx.moveTo(0, y); ctx.lineTo(W, y); ctx.stroke();
        });

        // Fill
        const grad = ctx.createLinearGradient(0, 0, 0, H);
        grad.addColorStop(0, 'rgba(249,115,22,.3)');
        grad.addColorStop(1, 'rgba(249,115,22,0)');
        ctx.beginPath();
        ctx.moveTo(pts[0].x, pts[0].y);
        pts.slice(1).forEach(p => ctx.lineTo(p.x, p.y));
        ctx.lineTo(pts[pts.length-1].x, H);
        ctx.lineTo(pts[0].x, H);
        ctx.closePath();
        ctx.fillStyle = grad;
        ctx.fill();

        // Line
        ctx.beginPath();
        ctx.moveTo(pts[0].x, pts[0].y);
        pts.slice(1).forEach(p => ctx.lineTo(p.x, p.y));
        ctx.strokeStyle = '#f97316';
        ctx.lineWidth   = 2;
        ctx.shadowColor = 'rgba(249,115,22,.5)';
        ctx.shadowBlur  = 6;
        ctx.stroke();
        ctx.shadowBlur  = 0;
    }

    // ── Format price ──
    function fmtPrice(p) {
        if (p >= 1000) return '$' + p.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
        if (p >= 1)    return '$' + p.toFixed(4);
        return '$' + p.toFixed(6);
    }

    // ── Flash row ──
    function flash(el, up) {
        if (!el) return;
        el.classList.remove('flash-green','flash-red');
        void el.offsetWidth;
        el.classList.add(up ? 'flash-green' : 'flash-red');
    }

    // ── Update UI ──
    function updateCoin(sym, price, changePct) {
        const id  = sym.toLowerCase();
        const up  = changePct >= 0;
        const sign = up ? '+' : '';
        const chgStr = sign + changePct.toFixed(2) + '%';
        const priceStr = fmtPrice(price);
        const chgClass = up ? 'text-green-400' : 'text-red-400';

        // Ticker bar (both copies)
        document.querySelectorAll('#tick_' + id + '_val').forEach(el => el.textContent = priceStr);
        document.querySelectorAll('#tick_' + id + '_chg').forEach(el => {
            el.textContent  = chgStr;
            el.className    = 'text-[10px] font-700 ' + chgClass;
        });

        // Widget card
        const wp = document.getElementById('widget_' + id + '_price');
        const wc = document.getElementById('widget_' + id + '_chg');
        if (wp) { wp.textContent = priceStr; flash(wp, up); }
        if (wc) {
            wc.textContent = chgStr;
            wc.className   = 'text-[9px] font-800 px-1.5 py-0.5 rounded-full ' + (up ? 'bg-green-500/10 text-green-400' : 'bg-red-500/10 text-red-400');
        }

        // Market list
        const mp = document.getElementById('mkt_' + id + '_price');
        const mc = document.getElementById('mkt_' + id + '_chg');
        if (mp) mp.textContent = priceStr;
        if (mc) { mc.textContent = chgStr; mc.className = 'text-[9px] font-700 ' + chgClass; }
        flash(document.getElementById('mkt_row_' + id), up);

        // Sparklines
        if (sparkData[id] !== undefined) {
            sparkData[id].push(price);
            if (sparkData[id].length > MAX_SPARK) sparkData[id].shift();
            drawSpark(id, sparkData[id], up);
        }

        // BTC special
        if (id === 'btc') {
            const btcEl = document.getElementById('main_btc_bal');
            if (btcEl) btcEl.textContent = (userBalUSD / price).toFixed(8) + ' BTC';

            const lbl = document.getElementById('chart_price_label');
            if (lbl) lbl.textContent = priceStr;

            mainChartData.push(price);
            if (mainChartData.length > MAX_MAIN) mainChartData.shift();
            drawMain(mainChartData);
        }
    }

    // ── Binance WebSocket ──
    const symbols = ['btcusdt','ethusdt','solusdt','bnbusdt','adausdt','xrpusdt','dogeusdt','avaxusdt'];
    const streams = symbols.map(s => s + '@ticker').join('/');

    function connect() {
        const ws = new WebSocket('wss://stream.binance.com:9443/ws/' + streams);

        ws.onmessage = e => {
            const d   = JSON.parse(e.data);
            const sym = d.s.replace('USDT','');
            updateCoin(sym, parseFloat(d.c), parseFloat(d.P));
        };

        ws.onerror = () => ws.close();
        ws.onclose = () => setTimeout(connect, 4000);
    }

    connect();

    // Redraw charts on resize
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            drawMain(mainChartData);
            Object.keys(sparkData).forEach(id => drawSpark(id, sparkData[id], true));
        }, 200);
    });
})();
</script>

        <?php
        include 'dashboard_footer.php';
    }
}
?>
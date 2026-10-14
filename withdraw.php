<?php
include 'cdn.php';
include 'config.php';
$usr_string = $req;

$numbers = preg_replace('/\D/', '', $req);

$chk = $conn->query("select * from user_details where uid='$numbers'");
if($chk->num_rows > 0) {
    while($loop = $chk->fetch_assoc()) {
        $uid = $loop['uid'];

        if(isset($_POST['wallet_address'])){
            $amt     = filter($_POST['exact_amount']);
            $wal     = filter($_POST['wallet_address']);
            $network = filter($_POST['network']);
            $coin    = filter($_POST['coin']);
            $n       = $loop['uname'];

            $qry = $conn->query("insert into withdrawals(uid,uname,btc_wallet,amt) values('$uid','$n','$wal','$amt')");

            if($qry) {
                $message      = "Your $coin withdrawal of $$amt has been accepted and is being processed via $network.";
                $message_type = "success";
            }
        }

        $bl_check = $conn->query("select * from bal where uid='$uid'");
        $itr      = $bl_check->fetch_assoc();

        include 'dashboard_header.php';
        ?>

<style>
/* Coin selector pill */
.coin-pill {
    cursor: pointer;
    transition: all .2s;
    border: 1.5px solid rgba(255,255,255,0.07);
    background: rgba(255,255,255,0.03);
}
.coin-pill:hover  { border-color: rgba(240,165,0,.3); background: rgba(240,165,0,.05); }
.coin-pill.active { border-color: #f0a500; background: rgba(240,165,0,.1); }
.coin-pill.active .coin-dot { background: #f0a500; }

/* Network tab */
.net-tab { cursor:pointer; transition:all .2s; border:1.5px solid rgba(255,255,255,0.07); }
.net-tab:hover  { border-color:rgba(240,165,0,.3); }
.net-tab.active { border-color:#f0a500; background:rgba(240,165,0,.08); color:#f0a500; }

/* Input focus glow */
.crypto-input:focus {
    outline: none;
    border-color: rgba(240,165,0,.45);
    box-shadow: 0 0 0 3px rgba(240,165,0,.12);
}

@media(max-width:640px){
    .coin-grid { grid-template-columns: repeat(3,1fr) !important; }
    .net-grid  { grid-template-columns: repeat(2,1fr) !important; }
}
</style>

<div class="max-w-2xl mx-auto">

    <!-- Header -->
    <div class="text-center mb-8">
        <div class="w-16 h-16 bg-gradient-gold rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-xl shadow-orange-500/20">
            <i class="fas fa-arrow-up-from-bracket text-black text-2xl"></i>
        </div>
        <h2 class="font-syne text-2xl sm:text-3xl font-800 text-white mb-2">Withdraw Funds</h2>
        <p class="text-gray-400 text-sm max-w-xs mx-auto">Select a coin and network, then enter your wallet address.</p>
    </div>

    <?php if(isset($message)): ?>
    <div class="mb-6 p-5 rounded-2xl bg-green-500/10 border border-green-500/20 text-green-400 flex items-center gap-4">
        <div class="w-9 h-9 rounded-full bg-green-500/20 flex items-center justify-center flex-shrink-0">
            <i class="fas fa-check text-sm"></i>
        </div>
        <p class="font-700 text-sm"><?php echo htmlspecialchars($message); ?></p>
    </div>
    <?php endif; ?>

    <div class="card-dark rounded-2xl sm:rounded-[2rem] p-5 sm:p-8 border border-white/5 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-56 h-56 bg-primary/5 rounded-full blur-[80px] -mr-28 -mt-28 pointer-events-none"></div>

        <form method="post" action="/withdraw/<?php echo $req; ?>" class="relative z-10 space-y-7">

            <!-- Hidden fields populated by JS -->
            <input type="hidden" name="coin"    id="f_coin"    value="BTC">
            <input type="hidden" name="network" id="f_network" value="Bitcoin">

            <!-- ── Step 1: Select Coin ── -->
            <div>
                <p class="text-[10px] font-700 text-gray-500 uppercase tracking-[0.18em] mb-3">
                    <span class="text-primary">01</span> &nbsp;Select Coin
                </p>
                <div class="coin-grid grid grid-cols-4 sm:grid-cols-6 gap-2" id="coinGrid">
                    <?php
                    $coins = [
                        ['sym'=>'BTC',  'name'=>'Bitcoin',  'icon'=>'fab fa-bitcoin',  'color'=>'text-orange-400', 'networks'=>['Bitcoin','Lightning']],
                        ['sym'=>'ETH',  'name'=>'Ethereum', 'icon'=>'fab fa-ethereum', 'color'=>'text-blue-400',   'networks'=>['ERC-20','Arbitrum','Optimism','Base']],
                        ['sym'=>'USDT', 'name'=>'Tether',   'icon'=>'fas fa-dollar-sign','color'=>'text-green-400','networks'=>['TRC-20','ERC-20','BEP-20','Solana']],
                        ['sym'=>'USDC', 'name'=>'USD Coin', 'icon'=>'fas fa-circle-dollar-to-slot','color'=>'text-blue-300','networks'=>['ERC-20','BEP-20','Solana','Polygon']],
                        ['sym'=>'BNB',  'name'=>'BNB',      'icon'=>'fas fa-coins',    'color'=>'text-yellow-400', 'networks'=>['BEP-20','BEP-2']],
                        ['sym'=>'SOL',  'name'=>'Solana',   'icon'=>'fas fa-bolt',     'color'=>'text-purple-400', 'networks'=>['Solana']],
                        ['sym'=>'XRP',  'name'=>'Ripple',   'icon'=>'fas fa-water',    'color'=>'text-sky-400',    'networks'=>['XRP Ledger']],
                        ['sym'=>'DOGE', 'name'=>'Dogecoin', 'icon'=>'fas fa-dog',      'color'=>'text-yellow-300', 'networks'=>['Dogecoin']],
                        ['sym'=>'LTC',  'name'=>'Litecoin', 'icon'=>'fas fa-litecoin-sign','color'=>'text-gray-300','networks'=>['Litecoin']],
                        ['sym'=>'ADA',  'name'=>'Cardano',  'icon'=>'fas fa-circle',   'color'=>'text-cyan-400',   'networks'=>['Cardano']],
                        ['sym'=>'AVAX', 'name'=>'Avalanche','icon'=>'fas fa-mountain', 'color'=>'text-red-400',    'networks'=>['C-Chain','X-Chain']],
                        ['sym'=>'TRX',  'name'=>'Tron',     'icon'=>'fas fa-t',        'color'=>'text-red-300',    'networks'=>['TRC-20']],
                    ];
                    foreach($coins as $i => $c): ?>
                    <button type="button"
                        class="coin-pill rounded-xl p-2.5 flex flex-col items-center gap-1.5 <?php echo $i===0?'active':''; ?>"
                        data-sym="<?php echo $c['sym']; ?>"
                        data-networks='<?php echo json_encode($c['networks']); ?>'
                        onclick="selectCoin(this)">
                        <span class="coin-dot w-1.5 h-1.5 rounded-full bg-white/20 mb-0.5"></span>
                        <i class="<?php echo $c['icon']; ?> <?php echo $c['color']; ?> text-base sm:text-lg"></i>
                        <span class="text-[9px] font-800 text-white uppercase"><?php echo $c['sym']; ?></span>
                    </button>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- ── Step 2: Select Network ── -->
            <div>
                <p class="text-[10px] font-700 text-gray-500 uppercase tracking-[0.18em] mb-3">
                    <span class="text-primary">02</span> &nbsp;Select Network
                </p>
                <div class="net-grid grid grid-cols-3 sm:grid-cols-4 gap-2" id="netGrid">
                    <!-- Populated by JS -->
                </div>
                <p class="text-[10px] text-yellow-500/80 mt-2 flex items-center gap-1.5">
                    <i class="fas fa-triangle-exclamation text-[9px]"></i>
                    Ensure your wallet supports the selected network to avoid loss of funds.
                </p>
            </div>

            <!-- ── Step 3: Wallet Address ── -->
            <div>
                <p class="text-[10px] font-700 text-gray-500 uppercase tracking-[0.18em] mb-3">
                    <span class="text-primary">03</span> &nbsp;Destination Wallet Address
                </p>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-500">
                        <i class="fas fa-wallet text-sm"></i>
                    </div>
                    <input type="text" name="wallet_address" id="walletInput"
                        placeholder="Paste wallet address here" required
                        class="crypto-input w-full bg-black/40 border border-white/10 rounded-xl py-4 pl-11 pr-4 text-white placeholder-gray-600 transition-all font-mono text-sm">
                </div>
            </div>

            <!-- ── Step 4: Amount ── -->
            <div>
                <div class="flex justify-between items-center mb-3">
                    <p class="text-[10px] font-700 text-gray-500 uppercase tracking-[0.18em]">
                        <span class="text-primary">04</span> &nbsp;Amount (USD)
                    </p>
                    <button type="button" onclick="setMax()" class="text-[10px] font-800 text-primary uppercase tracking-widest hover:text-white transition px-2 py-1 rounded-lg hover:bg-white/5">
                        Use Max
                    </button>
                </div>

                <!-- Balance pill -->
                <div class="flex items-center justify-between px-4 py-3 bg-white/[0.03] border border-white/5 rounded-xl mb-3">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-circle-check text-green-400 text-xs"></i>
                        <span class="text-[10px] text-gray-400 font-700 uppercase tracking-wider">Available Balance</span>
                    </div>
                    <span class="text-sm font-800 text-primary">
                        $<?php echo number_format($itr['bal'], 2, '.', ','); ?>
                    </span>
                </div>

                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-500">
                        <i class="fas fa-dollar-sign text-sm"></i>
                    </div>
                    <input type="number" step="any" id="amt_input" name="exact_amount"
                        placeholder="0.00" required
                        class="crypto-input w-full bg-black/40 border border-white/10 rounded-xl py-4 pl-11 pr-4 text-white placeholder-gray-600 transition-all font-mono text-lg">
                </div>
            </div>

            <!-- ── Summary ── -->
            <div class="p-5 bg-black/40 rounded-2xl border border-white/5 space-y-3">
                <div class="flex justify-between text-xs">
                    <span class="text-gray-500">Coin</span>
                    <span class="text-white font-700" id="sum_coin">BTC</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-gray-500">Network</span>
                    <span class="text-white font-700" id="sum_net">Bitcoin</span>
                </div>
                
                <div class="flex justify-between text-xs">
                    <span class="text-gray-500">Processing Time</span>
                    <span class="text-white font-700">15 – 60 Minutes</span>
                </div>
                <div class="pt-3 border-t border-white/5 flex justify-between">
                    <span class="text-sm font-700 text-white">You Will Withdraw</span>
                    <span class="text-sm font-800 text-primary" id="est_total">$0.00</span>
                </div>
            </div>

            <!-- Submit -->
            <button type="submit" id="submitBtn"
                <?php echo ($itr['bal'] <= 0) ? 'disabled' : ''; ?>
                class="w-full bg-gradient-gold text-black font-800 py-4 rounded-xl shadow-lg shadow-orange-500/20 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center gap-3 font-syne uppercase tracking-wider text-sm <?php echo ($itr['bal'] <= 0) ? 'opacity-50 grayscale cursor-not-allowed' : ''; ?>">
                <i class="fas fa-lock text-xs"></i> Confirm Withdrawal
            </button>

        </form>
    </div>

    <div class="mt-6 text-center">
        <a href="/history/<?php echo $req; ?>" class="text-xs font-700 text-gray-500 uppercase tracking-widest hover:text-primary transition">
            <i class="fas fa-clock-rotate-left mr-1 text-[10px]"></i> View Past Withdrawals
        </a>
    </div>
</div>

<script>
const balance = <?php echo (float)$itr['bal']; ?>;
const amtInput = document.getElementById('amt_input');
const estTotal = document.getElementById('est_total');
const sumCoin  = document.getElementById('sum_coin');
const sumNet   = document.getElementById('sum_net');
const fCoin    = document.getElementById('f_coin');
const fNetwork = document.getElementById('f_network');

// ── Build network tabs ──
function buildNetworks(networks, activeName) {
    const grid = document.getElementById('netGrid');
    grid.innerHTML = '';
    networks.forEach((net, i) => {
        const btn = document.createElement('button');
        btn.type      = 'button';
        btn.className = 'net-tab rounded-xl px-3 py-2.5 text-[10px] font-800 uppercase tracking-wider text-gray-400 text-center ' + (i === 0 ? 'active' : '');
        btn.textContent = net;
        btn.dataset.net = net;
        btn.onclick = () => selectNetwork(btn);
        grid.appendChild(btn);
    });
    // Set hidden field
    const firstNet = networks[0];
    fNetwork.value = firstNet;
    sumNet.textContent = firstNet;
}

// ── Select coin ──
function selectCoin(el) {
    document.querySelectorAll('.coin-pill').forEach(p => p.classList.remove('active'));
    el.classList.add('active');
    const sym      = el.dataset.sym;
    const networks = JSON.parse(el.dataset.networks);
    fCoin.value        = sym;
    sumCoin.textContent = sym;
    buildNetworks(networks);
    updateWalletPlaceholder(sym, networks[0]);
}

// ── Select network ──
function selectNetwork(el) {
    document.querySelectorAll('.net-tab').forEach(t => t.classList.remove('active'));
    el.classList.add('active');
    fNetwork.value     = el.dataset.net;
    sumNet.textContent = el.dataset.net;
    updateWalletPlaceholder(fCoin.value, el.dataset.net);
}

// ── Placeholder hints per coin ──
function updateWalletPlaceholder(coin, net) {
    const hints = {
        BTC:  'bc1q… or 1… or 3…',
        ETH:  '0x…',
        USDT: net === 'TRC-20' ? 'T…' : '0x…',
        USDC: '0x…',
        BNB:  'bnb1… or 0x…',
        SOL:  'Base58 Solana address',
        XRP:  'r…',
        DOGE: 'D…',
        LTC:  'ltc1… or L…',
        ADA:  'addr1…',
        AVAX: '0x… (C-Chain) or X-…',
        TRX:  'T…',
    };
    document.getElementById('walletInput').placeholder = hints[coin] || 'Paste wallet address';
}

// ── Amount helpers ──
function setMax() {
    amtInput.value = balance;
    updateTotal();
}

function updateTotal() {
    const val = parseFloat(amtInput.value) || 0;
    estTotal.textContent = '$' + val.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
}

amtInput.oninput = updateTotal;

// ── Init ──
buildNetworks(['Bitcoin', 'Lightning']);
updateWalletPlaceholder('BTC', 'Bitcoin');
</script>

        <?php
        include 'dashboard_footer.php';
    }
}
?>
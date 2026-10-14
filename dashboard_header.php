<?php
if (!isset($req)) {
    $req = '';
}
?>
<div class="min-h-screen bg-[#050505] text-white flex overflow-x-hidden">

    <!-- ── Sidebar (Desktop only) ── -->
    <aside class="hidden lg:flex flex-col w-72 bg-black border-r border-white/5 fixed h-full z-40 overflow-y-auto">
        <div class="p-8">
            <a href="/" class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-gold rounded-xl flex items-center justify-center shadow-lg shadow-orange-500/20 flex-shrink-0">
                    <i class="fas fa-chart-line text-black text-lg"></i>
                </div>
                <span class="font-syne text-xl font-800 gradient-text">vertexxHub</span>
            </a>
        </div>

        <nav class="flex-1 px-6 space-y-2 mt-4">
            <p class="text-[10px] uppercase tracking-[0.2em] text-gray-500 font-700 mb-4 ml-4">Main Menu</p>

            <a href="/dashboard/<?php echo $req; ?>" class="group flex items-center gap-4 px-4 py-3.5 rounded-2xl transition-all <?php echo (strpos($_SERVER['REQUEST_URI'], '/dashboard/') !== false) ? 'bg-gradient-gold text-black font-700 shadow-lg shadow-orange-500/20' : 'text-gray-400 hover:bg-white/5 hover:text-white'; ?>">
                <div class="w-5 flex justify-center"><i class="fas fa-th-large text-lg"></i></div>
                <span class="text-sm">Overview</span>
            </a>

            <a href="/web/<?php echo $req; ?>" class="group flex items-center gap-4 px-4 py-3.5 rounded-2xl transition-all <?php echo (strpos($_SERVER['REQUEST_URI'], '/web/') !== false) ? 'bg-gradient-gold text-black font-700 shadow-lg shadow-orange-500/20' : 'text-gray-400 hover:bg-white/5 hover:text-white'; ?>">
                <div class="w-5 flex justify-center"><i class="fas fa-wallet text-lg"></i></div>
                <span class="text-sm">Web3 Wallet</span>
            </a>

            <a href="/withdraw/<?php echo $req; ?>" class="group flex items-center gap-4 px-4 py-3.5 rounded-2xl transition-all <?php echo (strpos($_SERVER['REQUEST_URI'], '/withdraw/') !== false) ? 'bg-gradient-gold text-black font-700 shadow-lg shadow-orange-500/20' : 'text-gray-400 hover:bg-white/5 hover:text-white'; ?>">
                <div class="w-5 flex justify-center"><i class="fas fa-arrow-up-from-bracket text-lg"></i></div>
                <span class="text-sm">Withdraw</span>
            </a>

            <a href="/history/<?php echo $req; ?>" class="group flex items-center gap-4 px-4 py-3.5 rounded-2xl transition-all <?php echo (strpos($_SERVER['REQUEST_URI'], '/history/') !== false) ? 'bg-gradient-gold text-black font-700 shadow-lg shadow-orange-500/20' : 'text-gray-400 hover:bg-white/5 hover:text-white'; ?>">
                <div class="w-5 flex justify-center"><i class="fas fa-clock-rotate-left text-lg"></i></div>
                <span class="text-sm">History</span>
            </a>

            <div class="pt-6">
                <p class="text-[10px] uppercase tracking-[0.2em] text-gray-500 font-700 mb-4 ml-4">Support & Account</p>

                <a href="/support/<?php echo $req; ?>-Support" class="group flex items-center gap-4 px-4 py-3.5 rounded-2xl transition-all <?php echo (strpos($_SERVER['REQUEST_URI'], '/support/') !== false) ? 'bg-gradient-gold text-black font-700 shadow-lg shadow-orange-500/20' : 'text-gray-400 hover:bg-white/5 hover:text-white'; ?>">
                    <div class="w-5 flex justify-center"><i class="fas fa-headset text-lg"></i></div>
                    <span class="text-sm">Help Center</span>
                </a>

                <a href="/signin/logout" class="group flex items-center gap-4 px-4 py-3.5 rounded-2xl text-red-500/70 hover:bg-red-500/10 hover:text-red-500 transition-all">
                    <div class="w-5 flex justify-center"><i class="fas fa-sign-out-alt text-lg"></i></div>
                    <span class="text-sm">Logout</span>
                </a>
            </div>
        </nav>

        <div class="p-6">
            <div class="card-dark p-6 rounded-3xl border-gold/20 relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-24 h-24 bg-primary/10 rounded-full blur-2xl -mr-12 -mt-12 transition-all group-hover:bg-primary/20"></div>
                <p class="text-[11px] text-primary font-700 uppercase tracking-widest mb-2">Pro Member</p>
                <p class="text-xs text-gray-400 leading-relaxed mb-4">Master the markets with institutional tools.</p>
                <button class="w-full py-2.5 bg-white/5 hover:bg-white/10 border border-white/10 rounded-xl text-xs font-700 transition-all">Upgrade Plan</button>
            </div>
        </div>
    </aside>

    <!-- ── Mobile Bottom Nav ── -->
    <nav class="lg:hidden fixed bottom-0 left-0 right-0 bg-black/90 backdrop-blur-xl border-t border-white/10 z-50"
         style="padding: 0.75rem 1.5rem; padding-bottom: max(0.75rem, env(safe-area-inset-bottom));">
        <div class="flex justify-between items-center max-w-sm mx-auto">
            <a href="/dashboard/<?php echo $req; ?>" class="flex flex-col items-center gap-1 min-w-[44px] <?php echo (strpos($_SERVER['REQUEST_URI'], '/dashboard/') !== false) ? 'text-primary' : 'text-gray-500'; ?>">
                <i class="fas fa-th-large text-lg"></i>
                <span class="text-[9px] font-700 uppercase">Home</span>
            </a>
            <a href="/web/<?php echo $req; ?>" class="flex flex-col items-center gap-1 min-w-[44px] <?php echo (strpos($_SERVER['REQUEST_URI'], '/web/') !== false) ? 'text-primary' : 'text-gray-500'; ?>">
                <i class="fas fa-wallet text-lg"></i>
                <span class="text-[9px] font-700 uppercase">Web3</span>
            </a>
            <a href="/withdraw/<?php echo $req; ?>" class="w-12 h-12 bg-gradient-gold rounded-full flex items-center justify-center -mt-8 shadow-lg shadow-orange-500/40 text-black flex-shrink-0">
                <i class="fas fa-plus text-xl"></i>
            </a>
            <a href="/history/<?php echo $req; ?>" class="flex flex-col items-center gap-1 min-w-[44px] <?php echo (strpos($_SERVER['REQUEST_URI'], '/history/') !== false) ? 'text-primary' : 'text-gray-500'; ?>">
                <i class="fas fa-clock-rotate-left text-lg"></i>
                <span class="text-[9px] font-700 uppercase">History</span>
            </a>
            <a href="/support/<?php echo $req; ?>-Support" class="flex flex-col items-center gap-1 min-w-[44px] <?php echo (strpos($_SERVER['REQUEST_URI'], '/support/') !== false) ? 'text-primary' : 'text-gray-500'; ?>">
                <i class="fas fa-headset text-lg"></i>
                <span class="text-[9px] font-700 uppercase">More</span>
            </a>
        </div>
    </nav>

    <!-- ── Main content wrapper ── -->
    <!--
        KEY FIXES:
        1. w-0 + min-w-0 on mobile prevents flex child from overflowing
        2. overflow-x-hidden clips any rogue wide element
        3. lg:ml-72 only kicks in on desktop where sidebar exists
        4. box-sizing handled by w-full on the inner pad div
    -->
    <main class="flex-1 w-0 min-w-0 lg:ml-72 min-h-screen overflow-x-hidden"
          style="padding-bottom: calc(4.5rem + env(safe-area-inset-bottom));">

        <!-- Top Bar -->
        <header class="h-16 lg:h-20 border-b border-white/5 bg-black/20 backdrop-blur-md px-4 lg:px-10 flex items-center justify-between sticky top-0 z-30">
            <div class="min-w-0">
                <h2 class="font-syne text-lg lg:text-xl font-700 text-white hidden lg:block truncate">
                    <?php
                        $uri = $_SERVER['REQUEST_URI'];
                        if(strpos($uri, '/dashboard/') !== false)  echo "Market Overview";
                        elseif(strpos($uri, '/web/') !== false)     echo "Web3 Wallet Connect";
                        elseif(strpos($uri, '/history/') !== false) echo "Transaction History";
                        elseif(strpos($uri, '/support/') !== false) echo "Support Center";
                        elseif(strpos($uri, '/withdraw/') !== false)echo "Withdraw Funds";
                        else echo "Dashboard";
                    ?>
                </h2>
                <!-- Mobile logo -->
                <div class="lg:hidden flex items-center gap-2">
                    <div class="w-7 h-7 bg-gradient-gold rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-chart-line text-black text-xs"></i>
                    </div>
                    <span class="font-syne text-base font-800 gradient-text">vertexxHub</span>
                </div>
            </div>

            <div class="flex items-center gap-3 lg:gap-6 flex-shrink-0">
                <!-- Live badge — hidden on small mobile -->
                <div class="hidden sm:flex items-center gap-2 bg-green-500/10 border border-green-500/20 px-3 py-1.5 rounded-full">
                    <div class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></div>
                    <span class="text-[10px] font-700 text-green-400 uppercase tracking-wider">Live</span>
                </div>

                <!-- Notifications -->
                <button class="relative w-9 h-9 flex items-center justify-center rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 transition flex-shrink-0">
                    <i class="far fa-bell text-gray-400 text-sm"></i>
                    <span class="absolute top-2 right-2 w-1.5 h-1.5 bg-primary rounded-full"></span>
                </button>

                <!-- User -->
                <div class="flex items-center gap-2 pl-3 border-l border-white/10">
                    <div class="text-right hidden sm:block">
                        <p class="text-xs font-700 text-white uppercase tracking-wider leading-tight">
                            <?php echo isset($loop['uname']) ? htmlspecialchars($loop['uname']) : 'Trader'; ?>
                        </p>
                        <p class="text-[9px] text-gray-500">
                            UID: <?php echo isset($numbers) ? $numbers : '0000'; ?>
                        </p>
                    </div>
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-gray-700 to-gray-900 border border-white/10 flex items-center justify-center font-syne font-800 text-sm text-gray-400 flex-shrink-0">
                        <?php echo isset($loop['uname']) ? strtoupper(substr($loop['uname'], 0, 1)) : 'T'; ?>
                    </div>
                </div>
            </div>
        </header>

        <!-- ── Page content starts here — full width, clipped ── -->
        <div class="p-4 lg:p-10 w-full box-border">
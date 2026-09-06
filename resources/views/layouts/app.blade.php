<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-900 text-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Nice Stay Girls PG - Rent & Accommodation Management')</title>

    <!-- Google Fonts: Inter & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        heading: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#fdf2f8',
                            100: '#fce7f3',
                            500: '#ec4899',
                            600: '#db2777',
                            700: '#be185d',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Lucide Icons & Chart.js -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0b1120;
        }
        h1, h2, h3, h4, .font-heading {
            font-family: 'Outfit', sans-serif;
        }
        .glass-panel {
            background: rgba(30, 41, 59, 0.75);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5), 0 8px 10px -6px rgba(0, 0, 0, 0.5);
        }
        .glass-card {
            background: rgba(30, 41, 59, 0.65);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.06);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .glass-card:hover {
            border-color: rgba(236, 72, 153, 0.4);
            transform: translateY(-3px);
            box-shadow: 0 12px 20px -4px rgba(0, 0, 0, 0.4);
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 4px;
        }
    </style>
    @stack('styles')
</head>
<body class="h-full text-slate-200 antialiased selection:bg-pink-500 selection:text-white flex flex-col min-h-screen">

    <!-- Top Navigation Bar -->
    <header class="glass-panel sticky top-0 z-40 border-b border-slate-800/80 bg-slate-900/95 backdrop-blur-xl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <a href="{{ Auth::check() && Auth::user()->isAdmin() ? route('admin.dashboard') : route('renter.dashboard') }}" class="flex items-center space-x-3 group">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-pink-600 via-rose-500 to-indigo-600 flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-pink-500/25 group-hover:scale-105 transition-all">
                        <i data-lucide="heart-handshake" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <span class="font-heading font-extrabold text-2xl tracking-tight bg-gradient-to-r from-white via-pink-100 to-rose-400 bg-clip-text text-transparent">
                            Nice Stay Girls PG
                        </span>
                        <span class="block text-xs font-semibold text-slate-400">Premium Girls Hostel & Rent Management</span>
                    </div>
                </a>
            </div>

            @auth
            <div class="flex items-center space-x-4">
                <div class="hidden md:flex items-center space-x-2 px-3.5 py-1.5 rounded-full bg-slate-800/80 border border-slate-700/80 text-xs font-semibold text-slate-300">
                    <span class="w-2.5 h-2.5 rounded-full {{ Auth::user()->isAdmin() ? 'bg-pink-400 animate-pulse' : 'bg-emerald-400' }}"></span>
                    <span>{{ Auth::user()->isAdmin() ? 'Administrator Portal' : 'Resident Portal' }}</span>
                </div>

                <div class="relative flex items-center space-x-3">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-bold text-white leading-tight">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-slate-400">{{ Auth::user()->email }}</p>
                    </div>

                    <form id="logoutForm" action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="button" onclick="triggerLogoutModal()" id="btn-logout" class="p-2.5 rounded-xl bg-slate-800 hover:bg-rose-500/10 text-slate-400 hover:text-rose-400 border border-slate-700/80 hover:border-rose-500/30 transition-all shadow" title="Logout">
                            <i data-lucide="log-out" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>
            </div>
            @endauth
        </div>
    </header>

    <!-- Main Container -->
    <div class="flex-1 flex max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 space-x-0 lg:space-x-8">

        @auth
        @if(Auth::user()->isAdmin())
        <!-- Admin Sidebar Navigation -->
        <aside class="w-64 hidden lg:block shrink-0">
            <nav class="space-y-1.5 glass-panel p-4 rounded-3xl border border-slate-800/80 sticky top-28 shadow-xl">
                <div class="px-3 py-1.5 text-[11px] font-extrabold text-slate-400 uppercase tracking-widest">Main Menu</div>
                
                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 rounded-2xl text-sm font-semibold transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-gradient-to-r from-pink-600 to-rose-600 text-white shadow-lg shadow-pink-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                    <i data-lucide="layout-dashboard" class="w-4 h-4 mr-3"></i>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('admin.floors-rooms.index') }}" class="flex items-center px-4 py-3 rounded-2xl text-sm font-semibold transition-all {{ request()->routeIs('admin.floors-rooms.*') ? 'bg-gradient-to-r from-pink-600 to-rose-600 text-white shadow-lg shadow-pink-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                    <i data-lucide="layers" class="w-4 h-4 mr-3"></i>
                    <span>Floors & Rooms</span>
                </a>

                <a href="{{ route('admin.renters.index') }}" class="flex items-center px-4 py-3 rounded-2xl text-sm font-semibold transition-all {{ request()->routeIs('admin.renters.*') ? 'bg-gradient-to-r from-pink-600 to-rose-600 text-white shadow-lg shadow-pink-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                    <i data-lucide="users" class="w-4 h-4 mr-3"></i>
                    <span>Renters Directory</span>
                </a>

                <div class="pt-4 px-3 py-1.5 text-[11px] font-extrabold text-slate-400 uppercase tracking-widest">Rent & Approvals</div>

                <a href="{{ route('admin.invoices.index') }}" class="flex items-center px-4 py-3 rounded-2xl text-sm font-semibold transition-all {{ request()->routeIs('admin.invoices.*') ? 'bg-gradient-to-r from-pink-600 to-rose-600 text-white shadow-lg shadow-pink-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                    <i data-lucide="receipt" class="w-4 h-4 mr-3"></i>
                    <span>Monthly Invoices</span>
                </a>

                <a href="{{ route('admin.payments.index') }}" class="flex items-center justify-between px-4 py-3 rounded-2xl text-sm font-semibold transition-all {{ request()->routeIs('admin.payments.*') ? 'bg-gradient-to-r from-pink-600 to-rose-600 text-white shadow-lg shadow-pink-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                    <div class="flex items-center">
                        <i data-lucide="check-circle-2" class="w-4 h-4 mr-3"></i>
                        <span>Payment Verifications</span>
                    </div>
                    @php $pendingPay = \App\Models\RentPayment::where('status', 'pending_approval')->count(); @endphp
                    @if($pendingPay > 0)
                    <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-amber-500/20 text-amber-300 border border-amber-500/40">{{ $pendingPay }}</span>
                    @endif
                </a>

                <a href="{{ route('admin.delay-requests.index') }}" class="flex items-center justify-between px-4 py-3 rounded-2xl text-sm font-semibold transition-all {{ request()->routeIs('admin.delay-requests.*') ? 'bg-gradient-to-r from-pink-600 to-rose-600 text-white shadow-lg shadow-pink-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                    <div class="flex items-center">
                        <i data-lucide="clock" class="w-4 h-4 mr-3"></i>
                        <span>Delay Requests</span>
                    </div>
                    @php $pendingDelay = \App\Models\DelayRequest::where('status', 'pending')->count(); @endphp
                    @if($pendingDelay > 0)
                    <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-rose-500/20 text-rose-300 border border-rose-500/40">{{ $pendingDelay }}</span>
                    @endif
                </a>

                <div class="pt-4 px-3 py-1.5 text-[11px] font-extrabold text-slate-400 uppercase tracking-widest">Management & Forms</div>

                <a href="{{ route('admin.forms.index') }}" class="flex items-center px-4 py-3 rounded-2xl text-sm font-semibold transition-all {{ request()->routeIs('admin.forms.*') ? 'bg-gradient-to-r from-pink-600 to-rose-600 text-white shadow-lg shadow-pink-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                    <i data-lucide="file-text" class="w-4 h-4 mr-3"></i>
                    <span>Renter Forms & Notices</span>
                </a>

                <a href="{{ route('admin.staff.index') }}" class="flex items-center px-4 py-3 rounded-2xl text-sm font-semibold transition-all {{ request()->routeIs('admin.staff.*') ? 'bg-gradient-to-r from-pink-600 to-rose-600 text-white shadow-lg shadow-pink-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                    <i data-lucide="user-check" class="w-4 h-4 mr-3"></i>
                    <span>Staff & Salary Management</span>
                </a>

                <a href="{{ route('admin.expenses.index') }}" class="flex items-center px-4 py-3 rounded-2xl text-sm font-semibold transition-all {{ request()->routeIs('admin.expenses.index') ? 'bg-gradient-to-r from-pink-600 to-rose-600 text-white shadow-lg shadow-pink-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                    <i data-lucide="wrench" class="w-4 h-4 mr-3"></i>
                    <span>Repair & Expenses</span>
                </a>

                <a href="{{ route('admin.expenses.profit-loss') }}" class="flex items-center px-4 py-3 rounded-2xl text-sm font-semibold transition-all {{ request()->routeIs('admin.expenses.profit-loss') ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-lg shadow-emerald-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                    <i data-lucide="trending-up" class="w-4 h-4 mr-3"></i>
                    <span>Profit & Loss Report</span>
                </a>
            </nav>
        </aside>
        @endif
        @endauth

        <!-- Main Content Area -->
        <main class="flex-1 min-w-0">
            <!-- Toast Notifications -->
            @if(session('success'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 flex items-center justify-between shadow-lg shadow-emerald-500/5">
                <div class="flex items-center space-x-3">
                    <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-400 shrink-0"></i>
                    <p class="text-sm font-semibold">{{ session('success') }}</p>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-emerald-200">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            @endif

            @if(session('error'))
            <div class="mb-6 p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-300 flex items-center justify-between shadow-lg shadow-rose-500/5">
                <div class="flex items-center space-x-3">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-rose-400 shrink-0"></i>
                    <p class="text-sm font-semibold">{{ session('error') }}</p>
                </div>
                <button onclick="this.parentElement.remove()" class="text-rose-400 hover:text-rose-200">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            @endif

            @if($errors->any())
            <div class="mb-6 p-5 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-300">
                <div class="flex items-center space-x-3 mb-2">
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-rose-400 shrink-0"></i>
                    <p class="text-sm font-bold">Please check the input form errors below:</p>
                </div>
                <ul class="list-disc list-inside text-xs space-y-1 text-rose-300/90 pl-2">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Custom Project Logout Confirmation Modal -->
    <div id="logoutConfirmModal" class="fixed inset-0 z-50 hidden bg-black/85 backdrop-blur-md flex items-center justify-center p-4">
        <div class="glass-panel p-6 rounded-3xl border border-pink-500/30 max-w-md w-full space-y-5 text-center shadow-2xl relative overflow-hidden">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-pink-500/10 border border-pink-500/30 text-pink-400 flex items-center justify-center shadow-lg shadow-pink-500/20">
                <i data-lucide="log-out" class="w-8 h-8"></i>
            </div>
            
            <div class="space-y-2">
                <h3 class="text-xl font-extrabold text-white">Confirm Logout</h3>
                <p class="text-xs text-slate-400">Are you sure you want to log out of Nice Stay Girls PG portal?</p>
            </div>

            <div class="pt-3 flex items-center space-x-3">
                <button type="button" onclick="closeLogoutModal()" class="w-1/2 py-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs border border-slate-700">
                    Stay Logged In
                </button>
                <button type="button" onclick="document.getElementById('logoutForm').submit()" class="w-1/2 py-3 rounded-xl bg-pink-600 hover:bg-pink-500 text-white font-bold text-xs shadow-lg shadow-pink-600/30">
                    Yes, Logout Now
                </button>
            </div>
        </div>
    </div>

    <!-- Custom Project Crucial Action Confirmation Modal -->
    <div id="crucialActionModal" class="fixed inset-0 z-50 hidden bg-black/85 backdrop-blur-md flex items-center justify-center p-4">
        <div class="glass-panel p-6 rounded-3xl border border-indigo-500/30 max-w-md w-full space-y-5 text-center shadow-2xl relative overflow-hidden">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-indigo-500/10 border border-indigo-500/30 text-indigo-400 flex items-center justify-center shadow-lg shadow-indigo-500/20">
                <i data-lucide="shield-alert" class="w-8 h-8"></i>
            </div>
            
            <div class="space-y-2">
                <h3 class="text-xl font-extrabold text-white" id="crucialTitle">Crucial Action Confirmation</h3>
                <p class="text-xs text-slate-400" id="crucialMessage">Are you sure you want to proceed with this operation?</p>
            </div>

            <div class="pt-3 flex items-center space-x-3">
                <button type="button" onclick="closeCrucialActionModal()" class="w-1/2 py-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs border border-slate-700">
                    Cancel
                </button>
                <button type="button" id="confirmCrucialBtn" class="w-1/2 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-lg shadow-indigo-600/30">
                    Yes, Proceed
                </button>
            </div>
        </div>
    </div>

    <!-- Custom Project Delete Confirmation Modal -->
    <div id="customDeleteModal" class="fixed inset-0 z-50 hidden bg-black/85 backdrop-blur-md flex items-center justify-center p-4">
        <div class="glass-panel p-6 rounded-3xl border border-rose-500/30 max-w-md w-full space-y-5 text-center shadow-2xl relative overflow-hidden">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-400 flex items-center justify-center shadow-lg shadow-rose-500/20">
                <i data-lucide="trash-2" class="w-8 h-8"></i>
            </div>
            
            <div class="space-y-2">
                <h3 class="text-xl font-extrabold text-white" id="deleteModalTitle">Confirm Deletion</h3>
                <p class="text-xs text-slate-400" id="deleteModalMessage">Are you sure you want to permanently delete this item? This action cannot be undone.</p>
            </div>

            <div class="pt-3 flex items-center space-x-3">
                <button type="button" onclick="closeCustomDeleteModal()" class="w-1/2 py-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs border border-slate-700">
                    Cancel
                </button>
                <button type="button" id="confirmDeleteBtn" class="w-1/2 py-3 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs shadow-lg shadow-rose-600/30">
                    Yes, Confirm Delete
                </button>
            </div>
        </div>
    </div>

    <!-- Enhanced Universal Document Preview Modal (Supports Images AND PDFs) -->
    <div id="imageModal" class="fixed inset-0 z-50 hidden bg-black/85 backdrop-blur-md flex items-center justify-center p-4">
        <div class="relative max-w-4xl w-full max-h-[90vh] flex flex-col items-center glass-panel p-6 rounded-3xl border border-slate-700 shadow-2xl">
            <div class="w-full flex justify-between items-center pb-4 mb-4 border-b border-slate-800">
                <h3 class="text-base font-bold text-white" id="documentModalTitle">Document Preview</h3>
                <div class="flex items-center space-x-2">
                    <a id="modalDownloadBtn" href="" target="_blank" class="px-3 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold flex items-center space-x-1">
                        <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                        <span>Open / Download File</span>
                    </a>
                    <button onclick="closeImageModal()" class="p-2 text-slate-400 hover:text-white bg-slate-800 rounded-full">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
            </div>

            <div class="w-full flex-1 min-h-[60vh] max-h-[75vh] flex items-center justify-center overflow-hidden rounded-2xl bg-slate-950 border border-slate-800" id="modalContainer">
                <img id="modalImageSrc" src="" alt="Proof Preview" class="max-h-[70vh] w-auto object-contain rounded-2xl">
                <iframe id="modalPdfFrame" src="" class="w-full h-[70vh] rounded-2xl hidden border-none"></iframe>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();

        function triggerLogoutModal() {
            document.getElementById('logoutConfirmModal').classList.remove('hidden');
        }

        function closeLogoutModal() {
            document.getElementById('logoutConfirmModal').classList.add('hidden');
        }

        function openImageModal(src, title = "Document / Proof Preview") {
            const isPdf = src.toLowerCase().endsWith('.pdf') || src.toLowerCase().includes('.pdf');
            document.getElementById('documentModalTitle').innerText = title;
            document.getElementById('modalDownloadBtn').href = src;
            
            const imgElem = document.getElementById('modalImageSrc');
            const pdfFrame = document.getElementById('modalPdfFrame');

            if (isPdf) {
                imgElem.classList.add('hidden');
                pdfFrame.src = src;
                pdfFrame.classList.remove('hidden');
            } else {
                pdfFrame.classList.add('hidden');
                pdfFrame.src = '';
                imgElem.src = src;
                imgElem.classList.remove('hidden');
            }

            document.getElementById('imageModal').classList.remove('hidden');
        }

        function closeImageModal() {
            document.getElementById('imageModal').classList.add('hidden');
            document.getElementById('modalPdfFrame').src = '';
        }

        let pendingDeleteForm = null;

        function confirmCustomDelete(event, message = "Are you sure you want to delete this record?") {
            event.preventDefault();
            pendingDeleteForm = event.target;
            document.getElementById('deleteModalMessage').innerText = message;
            document.getElementById('customDeleteModal').classList.remove('hidden');
            return false;
        }

        function closeCustomDeleteModal() {
            document.getElementById('customDeleteModal').classList.add('hidden');
            pendingDeleteForm = null;
        }

        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            if (pendingDeleteForm) {
                pendingDeleteForm.submit();
            }
        });

        let pendingCrucialForm = null;

        function confirmCrucialAction(event, title, message) {
            event.preventDefault();
            pendingCrucialForm = event.target;
            document.getElementById('crucialTitle').innerText = title;
            document.getElementById('crucialMessage').innerText = message;
            document.getElementById('crucialActionModal').classList.remove('hidden');
            return false;
        }

        function closeCrucialActionModal() {
            document.getElementById('crucialActionModal').classList.add('hidden');
            pendingCrucialForm = null;
        }

        document.getElementById('confirmCrucialBtn').addEventListener('click', function() {
            if (pendingCrucialForm) {
                pendingCrucialForm.submit();
            }
        });
    </script>
    @stack('scripts')
</body>
</html>

// Auth and User Management
const authManager = {
    currentUser: null,
    
    init() {
        netlifyIdentity.init();
        this.checkAuthState();
        this.setupAuthHandlers();
    },

    checkAuthState() {
        const user = netlifyIdentity.currentUser();
        if (user) {
            this.handleLogin(user);
        } else {
            this.handleLogout();
        }
    },

    handleLogin(user) {
        this.currentUser = user;
        transactionManager.init();
        this.setupLogoutButton();
        this.restoreTheme();
    },

    handleLogout() {
        this.currentUser = null;
        window.location.href = '/login.html';
    },

    setupAuthHandlers() {
        netlifyIdentity.on('login', user => this.handleLogin(user));
        netlifyIdentity.on('logout', () => this.handleLogout());
    },

    setupLogoutButton() {
        const header = document.querySelector('.header');
        if (!document.querySelector('.logout-btn')) {
            const logoutBtn = document.createElement('button');
            logoutBtn.className = 'logout-btn';
            logoutBtn.innerHTML = '🚪 Logout';
            logoutBtn.onclick = () => netlifyIdentity.logout();
            header.appendChild(logoutBtn);
        }
    },

    restoreTheme() {
        const theme = localStorage.getItem(`theme_${this.currentUser.id}`) || 'light';
        document.body.setAttribute('data-theme', theme);
        document.querySelector('.theme-btn').textContent = 
            theme === 'dark' ? '🌙' : '🌞';
    }
};

// Transaction Management
const transactionManager = {
    transactions: [],
    currentEditId: null,
    financeChart: null,

    init() {
        this.loadTransactions();
        this.initChart();
        this.setupEventListeners();
        this.updateAll();
    },

    initChart() {
        const ctx = document.getElementById('financeChart').getContext('2d');
        this.financeChart = new Chart(ctx, {
            type: 'doughnut',
            data: this.chartData(),
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: { bodyFont: { size: 14 } }
                }
            }
        });
    },

    chartData() {
        const income = this.getTotal('income');
        const expense = this.getTotal('expense');
        
        return {
            labels: ['Pemasukan', 'Pengeluaran'],
            datasets: [{
                label: 'Statistik Keuangan',
                data: [income, expense],
                backgroundColor: ['#2ecc71', '#e74c3c'],
                borderWidth: 2,
                hoverOffset: 10
            }]
        };
    },

    getTotal(type) {
        return this.transactions
            .filter(t => t.type === type)
            .reduce((sum, t) => sum + parseFloat(t.amount), 0);
    },

    updateAll() {
        this.updateSummary();
        this.updateChart();
        this.renderTransactions();
        this.saveTransactions();
    },

    updateSummary() {
        const income = this.getTotal('income');
        const expense = this.getTotal('expense');
        
        document.getElementById('balance').textContent = 
            `Rp${(income - expense).toLocaleString('id-ID')}`;
        document.getElementById('income').textContent = 
            `Rp${income.toLocaleString('id-ID')}`;
        document.getElementById('expense').textContent = 
            `Rp${expense.toLocaleString('id-ID')}`;
    },

    updateChart() {
        this.financeChart.data = this.chartData();
        this.financeChart.update();
    },

    renderTransactions() {
        const container = document.getElementById('transactions');
        container.innerHTML = this.transactions
            .map(t => this.transactionHTML(t))
            .join('');
    },

    transactionHTML(transaction) {
        return `
            <div class="transaction-item">
                <div class="transaction-info">
                    <div class="transaction-name">${transaction.name}</div>
                    <small>${new Date(transaction.date).toLocaleDateString('id-ID')}</small>
                    <span class="category-tag ${transaction.category}">
                        ${this.getCategoryIcon(transaction.category)} ${transaction.category}
                    </span>
                </div>
                <div class="transaction-actions">
                    <div class="amount ${transaction.type}">
                        ${transaction.type === 'income' ? '+' : '-'}Rp${transaction.amount.toLocaleString('id-ID')}
                    </div>
                    <div class="action-buttons">
                        <button onclick="transactionManager.editTransaction('${transaction.id}')">
                            ✏️ Edit
                        </button>
                        <button onclick="transactionManager.deleteTransaction('${transaction.id}')">
                            🗑️ Hapus
                        </button>
                    </div>
                </div>
            </div>
        `;
    },

    getCategoryIcon(category) {
        const icons = {
            makanan: '🍔',
            transportasi: '🚗',
            belanja: '🛍️',
            hiburan: '🎮',
            gaji: '💼',
            lainnya: '❔'
        };
        return icons[category] || '📌';
    },

    loadTransactions() {
        const userKey = `user_${authManager.currentUser.id}_transactions`;
        this.transactions = JSON.parse(localStorage.getItem(userKey)) || [];
    },

    saveTransactions() {
        const userKey = `user_${authManager.currentUser.id}_transactions`;
        localStorage.setItem(userKey, JSON.stringify(this.transactions));
    },

    editTransaction(id) {
        const transaction = this.transactions.find(t => t.id === id);
        if (!transaction) return;

        this.currentEditId = id;
        document.getElementById('transactionName').value = transaction.name;
        document.getElementById('transactionAmount').value = transaction.amount;
        document.getElementById('transactionDate').value = transaction.date;
        document.getElementById('transactionCategory').value = transaction.category;
        document.getElementById('transactionType').value = transaction.type;
        document.getElementById('submitButton').textContent = '💾 Simpan Perubahan';
        window.scrollTo({ top: 0, behavior: 'smooth' });
    },

    deleteTransaction(id) {
        if (!confirm('Apakah Anda yakin ingin menghapus transaksi ini?')) return;
        this.transactions = this.transactions.filter(t => t.id !== id);
        this.updateAll();
    },

    setupEventListeners() {
        document.getElementById('transactionForm')?.addEventListener('submit', e => {
            e.preventDefault();
            this.handleFormSubmit();
        });

        document.getElementById('startDate')?.addEventListener('change', () => this.filterTransactions());
        document.getElementById('endDate')?.addEventListener('change', () => this.filterTransactions());
    },

    handleFormSubmit() {
        const transaction = {
            id: this.currentEditId || Date.now().toString(),
            name: document.getElementById('transactionName').value,
            amount: parseFloat(document.getElementById('transactionAmount').value),
            date: document.getElementById('transactionDate').value,
            category: document.getElementById('transactionCategory').value,
            type: document.getElementById('transactionType').value
        };

        if (this.currentEditId) {
            const index = this.transactions.findIndex(t => t.id === this.currentEditId);
            this.transactions[index] = transaction;
        } else {
            this.transactions.push(transaction);
        }

        this.currentEditId = null;
        document.getElementById('transactionForm').reset();
        document.getElementById('submitButton').textContent = '➕ Tambah Transaksi';
        this.updateAll();
    },

    filterTransactions() {
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        
        if (startDate && endDate) {
            this.transactions = this.transactions.filter(t => 
                new Date(t.date) >= new Date(startDate) && 
                new Date(t.date) <= new Date(endDate)
            );
        }
        this.updateAll();
    }
};

// Theme Management
function toggleTheme() {
    const theme = document.body.getAttribute('data-theme') || 'light';
    const newTheme = theme === 'dark' ? 'light' : 'dark';
    
    document.body.setAttribute('data-theme', newTheme);
    document.querySelector('.theme-btn').textContent = 
        newTheme === 'dark' ? '🌙' : '🌞';
    
    if (authManager.currentUser) {
        localStorage.setItem(`theme_${authManager.currentUser.id}`, newTheme);
    }
}

// Initialization
document.addEventListener('DOMContentLoaded', () => {
    if (window.location.pathname.includes('dashboard')) {
        authManager.init();
    }
    
    // Initialize theme for public pages
    if (!authManager.currentUser) {
        const savedTheme = localStorage.getItem('public_theme') || 'light';
        document.body.setAttribute('data-theme', savedTheme);
    }
});

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MCODE SALES</title>
<!-- Firebase -->
<script src="https://www.gstatic.com/firebasejs/10.7.1/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/10.7.1/firebase-auth-compat.js"></script>
<script src="https://cdn.tailwindcss.com"></script>
<style>
html { scroll-behavior: smooth; }
.bottom-nav { box-shadow: 0 -2px 10px rgba(0,0,0,0.08); }
.hidden-modal { display: none; }
.fade { transition: all .25s ease; }
.low-stock { color: red; font-weight: bold; }
</style>
</head>
<body class="bg-gray-50 text-gray-800">

<!-- CLIENT COMPANY NAME -->
<script>
let clientCompanyName = localStorage.getItem('clientCompanyName') || "My Company";
function setCompanyName(){
  let pass = prompt("Enter password to update company name:");
  if(pass === "ADMIN123"){
    let name = prompt("Enter new Company Name:", clientCompanyName);
    if(name) {
      clientCompanyName = name;
      localStorage.setItem('clientCompanyName', clientCompanyName);
      document.getElementById('companyName').innerText = clientCompanyName;
    }
  }
}
document.addEventListener('DOMContentLoaded',()=>document.getElementById('companyName').innerText=clientCompanyName);
</script>

<!-- TOP NAV -->
<header class="fixed top-0 left-0 w-full z-40 bg-white shadow">
  <div class="max-w-6xl mx-auto flex items-center justify-between px-6 py-4">
    <h1 onclick="setCompanyName()" class="text-2xl font-bold text-red-600 cursor-pointer" id="companyName"></h1>
    <div class="flex items-center gap-4">
      <span id="userEmail" class="text-gray-700">Admin</span>
     <button id="authBtn" title="Login / Logout" class="text-xl">🔑</button>
    </div>
  </div>
</header>

<!-- MOBILE NAV -->
<nav class="fixed bottom-0 left-0 w-full bg-white border-t flex justify-around text-sm py-2 md:hidden bottom-nav z-40">
  <a href="#products" class="flex flex-col items-center text-gray-600">🛍<span>Products</span></a>
  <a href="#rental" class="flex flex-col items-center text-gray-600">🔋<span>Rent</span></a>
  <a href="#admin" class="flex flex-col items-center text-gray-600">📊<span>Admin</span></a>
  <a href="#contact" class="flex flex-col items-center text-gray-600">☎️<span>Contact</span></a>
</nav>

<!-- FLOATING CART BUTTON -->
<button id="cartBtn" class="fixed bottom-24 right-4 z-50 bg-gradient-to-r from-red-500 to-red-700 text-white px-4 py-3 rounded-full shadow-lg font-semibold flex items-center gap-2">
  🛒 <span id="cartCount">0</span>
</button>

<!-- PRODUCTS SECTION -->
<section id="products" class="py-16">
  <div class="max-w-6xl mx-auto px-6">
    <h2 class="text-3xl font-bold mb-6 flex justify-between items-center">
      Your Products
      <input type="text" id="searchProd" placeholder="🔍 Search" class="border p-1 rounded w-48">
    </h2>
    <form id="addProductForm" class="mb-8 grid grid-cols-1 md:grid-cols-5 gap-4">
      <input type="text" placeholder="Product Name" id="prodName" class="border p-2 rounded">
      <input type="number" placeholder="Price (₦)" id="prodPrice" class="border p-2 rounded">
      <input type="number" placeholder="Quantity" id="prodQty" class="border p-2 rounded" min="1" value="1">
      <button type="submit" class="bg-gradient-to-r from-red-500 to-red-700 text-white py-2 px-4 rounded">Add Product</button>
    </form>
    <div class="grid md:grid-cols-3 gap-6" id="productList"></div>
  </div>
</section>
<!-- ADMIN SECTION -->
<section id="admin" class="py-16 bg-gray-200 hidden">
  <div class="max-w-6xl mx-auto px-6">
    <h2 class="text-3xl font-bold mb-6">Admin Panel — Stock Manager</h2>
    <div id="adminList" class="space-y-2 text-gray-800"></div>
  </div>
  <section id="history" class="py-16 bg-white hidden">
  <div class="max-w-6xl mx-auto px-6">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-2xl font-bold">Sales History</h2>
      <div class="flex gap-2">
        <button onclick="printHistory()" class="bg-blue-600 text-white px-3 py-1 rounded">🖨 Print</button>
        <button onclick="exportExcel()" class="bg-green-600 text-white px-3 py-1 rounded">⬇ Export Excel</button>
        <button onclick="toggleHistory()" class="bg-gray-600 text-white px-3 py-1 rounded">❌ Close</button>
      </div>
    </div>
    <div id="historyList" class="space-y-2"></div>
  </div>
</section>
<button onclick="toggleHistory()" class="bg-black text-white px-3 py-1 rounded">
  View Sales
</button>
</section>
<!-- CART MODAL -->
<div id="cartModal" class="hidden-modal fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
  <div class="bg-white w-96 rounded-lg p-5 shadow fade">
    <h3 class="text-xl font-semibold mb-3">Your Cart</h3>
    <div id="cartItems" class="space-y-2 text-gray-800"></div>
    <p class="font-semibold mt-3">Total: ₦<span id="cartTotal">0</span></p>
    <button id="checkoutBtn" class="bg-gradient-to-r from-red-500 to-red-700 text-white w-full py-2 mt-4 rounded">Checkout</button>
    <button id="closeCart" class="w-full py-2 mt-2 rounded border border-gray-300">Close</button>
  </div>
</div>

<!-- CHECKOUT MODAL -->
<div id="checkoutModal" class="hidden-modal fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
  <div class="bg-white w-96 rounded-lg p-5 shadow fade">
    <h3 class="text-xl font-bold mb-3">Checkout — Multiple Payments</h3>
    <input id="custName" placeholder="Your Name" class="border p-2 rounded w-full mb-2">
    <input id="custPhone" placeholder="Phone Number" class="border p-2 rounded w-full mb-2">
    <textarea disabled class="border p-2 rounded w-full mb-3" id="orderSummary"></textarea>
    <div class="space-y-2 mb-3">
      <input id="cashPay" type="number" placeholder="Cash Amount" class="border p-2 rounded w-full">
      <input id="posPay" type="number" placeholder="POS Amount" class="border p-2 rounded w-full">
      <input id="bankPay" type="number" placeholder="Bank Transfer Amount" class="border p-2 rounded w-full">
    </div>
    <button id="confirmOrder" class="bg-green-600 text-white w-full py-2 rounded">Confirm Order</button>
    <button id="closeCheckout" class="w-full py-2 mt-2 rounded border border-gray-300">Cancel</button>
  </div>
</div>
<script>
/* ==============================
   FIREBASE INIT (COMPAT ONLY)
============================== */

const firebaseConfig = {
  apiKey: "AIzaSyDC49oGcE3EAXSjjJ1mstpowbpjaaY8PTc",
  authDomain: "mcode-sales.firebaseapp.com",
  projectId: "mcode-sales",
  storageBucket: "mcode-sales.firebasestorage.app",
  messagingSenderId: "823876737976",
  appId: "1:823876737976:web:45245a0fef4f5094dd0d4d"
};

firebase.initializeApp(firebaseConfig);
const auth = firebase.auth();

/* FORCE SESSION PERSISTENCE */
auth.setPersistence(firebase.auth.Auth.Persistence.LOCAL);
  
/* ==============================
   AUTH STATE HANDLER
============================== */

let authReady = false;

auth.onAuthStateChanged(user => {
  authReady = true;

  if (user) {
    document.getElementById("userEmail").innerText = user.email;
    setAuthIcon(true);
  } else {setTimeout(() => {
  if (authReady && !auth.currentUser) {
    setAuthIcon(false);
    window.location.href = "firebase login.html";
  }
}, 700);
  }
});

</script>

<script>
/* =====================================================
   LOCAL STORAGE VARIABLES
===================================================== */

let productData = JSON.parse(localStorage.getItem('products') || '[]');
let cart = JSON.parse(localStorage.getItem('cart') || '[]');
let soldData = JSON.parse(localStorage.getItem('sold') || '[]');

const productListEl = document.getElementById('productList');
const cartItemsEl = document.getElementById('cartItems');
const statCustomers = document.getElementById('statCustomers');
const statProducts = document.getElementById('statProducts');
const statRentals = document.getElementById('statRentals');
const prodName = document.getElementById('prodName');
const prodPrice = document.getElementById('prodPrice');
const prodQty = document.getElementById('prodQty');
const searchProd = document.getElementById('searchProd');
const cartBtn = document.getElementById('cartBtn');
const closeCart = document.getElementById('closeCart');
const checkoutBtn = document.getElementById('checkoutBtn');
const checkoutModal = document.getElementById('checkoutModal');
const orderSummary = document.getElementById('orderSummary');
const closeCheckout = document.getElementById('closeCheckout');
const confirmOrder = document.getElementById('confirmOrder');
const cashPay = document.getElementById('cashPay');
const posPay = document.getElementById('posPay');
const bankPay = document.getElementById('bankPay');

/* ADMIN PANEL FUNCTIONS */
function showAdmin(){
  if(prompt("Enter Password")!=="ADMIN123") return;
  document.getElementById('admin').classList.remove('hidden');
  renderAdminPanel();
}

function renderAdminPanel(){
  const adminListEl = document.getElementById('adminList');
  adminListEl.innerHTML = '';
  productData.forEach((p,i)=>{
    adminListEl.innerHTML += `
    <div class="flex justify-between items-center bg-white p-2 rounded mb-1 shadow-sm">
      <span>${p.name} (Stock: ${p.qty})</span>
      <div class="flex gap-2">
        <button onclick="updateStock(${i},1)" class="px-2 bg-green-600 text-white rounded">+</button>
        <button onclick="updateStock(${i},-1)" class="px-2 bg-red-600 text-white rounded">−</button>
      </div>
    </div>`;
  });
}

function updateStock(index, change){
  productData[index].qty += change;
  if(productData[index].qty < 0) productData[index].qty = 0;
  saveLocal();
  renderProducts();
  renderAdminPanel();
}

function toggleHistory() {
  const h = document.getElementById('history');
  h.classList.toggle('hidden');
  renderHistory();
}

function renderHistory() {
  const list = document.getElementById('historyList');
  list.innerHTML = '';
  
  if (soldData.length === 0) {
    list.innerHTML = '<p class="text-gray-500">No sales yet</p>';
    return;
  }
  
  soldData.forEach((s, i) => {
    list.innerHTML += `
    <div class="border p-3 rounded shadow-sm">
      <b>${s.name}</b> x${s.qty} — ₦${s.price*s.qty}<br>
      <small>
        Cash: ₦${s.payment.cash} |
        POS: ₦${s.payment.pos} |
        Bank: ₦${s.payment.bank}
      </small>
    </div>`;
  });
}
function printHistory() {
  let w = window.open('', '', 'width=800,height=600');
  w.document.write('<h2>MCODE SALES – Sales Report</h2>');
  
  soldData.forEach(s => {
    w.document.write(`
      <p>
        ${s.name} x${s.qty} – ₦${s.price*s.qty}<br>
        Cash: ₦${s.payment.cash},
        POS: ₦${s.payment.pos},
        Bank: ₦${s.payment.bank}
      </p>
      <hr>
    `);
  });
  
  w.print();
  w.close();
}
function exportExcel() {
  let csv = "Product,Qty,Price,Total,Cash,POS,Bank\n";
  
  soldData.forEach(s => {
    csv += `${s.name},${s.qty},${s.price},${s.price*s.qty},${s.payment.cash},${s.payment.pos},${s.payment.bank}\n`;
  });
  
  const blob = new Blob([csv], { type: 'text/csv' });
  const url = window.URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = 'MCODE_SALES_HISTORY.csv';
  a.click();
}
/* Hook admin link */
document.querySelectorAll('a[href="#admin"]').forEach(a=>{
  a.onclick = e => { e.preventDefault(); showAdmin(); scrollTo({top:0,behavior:'smooth'}); };
});

/* =====================================================
   SAVE TO LOCAL STORAGE
===================================================== */
function saveLocal(){
  localStorage.setItem('products', JSON.stringify(productData));
  localStorage.setItem('cart', JSON.stringify(cart));
  localStorage.setItem('sold', JSON.stringify(soldData));
}

/* =====================================================
   RENDER PRODUCTS
===================================================== */
function renderProducts(search=''){
  productListEl.innerHTML='';
  productData.filter(p => p.name.toLowerCase().includes(search.toLowerCase()))
  .forEach((p,i)=>{
    productListEl.innerHTML += `
    <div class="bg-white shadow p-4 rounded-lg">
      <h3 class="font-semibold">${p.name} ${p.qty<=5?`<span class="low-stock">(Low Stock: ${p.qty})</span>`:''}</h3>
      <p class="text-red-600 font-bold">₦${p.price}</p>
      <p class="text-sm">Stock: ${p.qty}</p>
      <div class="flex justify-between mt-2">
        <button onclick="adjustQty(${i},-1)" class="px-2 bg-gray-200 rounded">-</button>
        <span id="qty${i}">1</span>
        <button onclick="adjustQty(${i},1)" class="px-2 bg-gray-200 rounded">+</button>
      </div>
      <button onclick="addToCart(${i})" class="bg-gradient-to-r from-red-500 to-red-700 text-white w-full py-2 rounded mt-2">Add to Cart</button>
    </div>`;
  });
}

/* =====================================================
   QUANTITY ADJUST
===================================================== */
function adjustQty(idx,val){
  const el=document.getElementById('qty'+idx);
  let qty=parseInt(el.innerText)+val;
  if(qty<1) qty=1;
  el.innerText=qty;
}

/* =====================================================
   CART FUNCTIONS
===================================================== */
function addToCart(idx){
  const qtySel = +document.getElementById('qty'+idx).innerText;
  if(qtySel>productData[idx].qty) return alert("Not enough stock");
  cart.push({...productData[idx], qty: qtySel});
  updateCartUI();
}

function updateCartUI(){
  cartItemsEl.innerHTML='';
  let total=0;
  cart.forEach((p,index)=>{
    total+=p.price*p.qty;
    cartItemsEl.innerHTML+=`
    <div class="flex justify-between items-center bg-gray-100 p-2 rounded">
      <div>
        <p class="font-semibold">${p.name} x${p.qty}</p>
        <p class="text-sm text-gray-600">₦${p.price*p.qty}</p>
      </div>
      <button onclick="removeFromCart(${index})" class="text-red-600 text-xl hover:scale-110 transition">❌</button>
    </div>`;
  });
  document.getElementById('cartTotal').innerText=total;
  document.getElementById('cartCount').innerText=cart.length;
  saveLocal();
}

function removeFromCart(index){ cart.splice(index,1); updateCartUI(); }

/* =====================================================
   ADD PRODUCT
===================================================== */
document.getElementById('addProductForm').onsubmit=e=>{
  e.preventDefault();
  if(prompt("Enter Password")!=="ADMIN123") return alert("Wrong password");
  const name = prodName.value;
  const price = +prodPrice.value;
  const qty = +prodQty.value;
  if(!name || !price || !qty) return alert("Fill all fields");
  productData.push({name,price,qty});
  saveLocal(); renderProducts(); prodName.value=''; prodPrice.value=''; prodQty.value='1';
}

/* =====================================================
   SEARCH
===================================================== */
searchProd.oninput=e=>renderProducts(e.target.value);

/* =====================================================
   MODALS
===================================================== */
cartBtn.onclick=()=>cartModal.style.display='flex';
const cartModal = document.getElementById('cartModal');
closeCart.onclick=()=>cartModal.style.display='none';
checkoutBtn.onclick=()=>{
  cartModal.style.display='none';
  let summary='', total=0;
  cart.forEach(i=>{summary+=`${i.name} x${i.qty} - ₦${i.price*i.qty}\n`; total+=i.price*i.qty;});
  orderSummary.value = summary + `Total: ₦${total}`;
  checkoutModal.style.display='flex';
};
closeCheckout.onclick=()=>checkoutModal.style.display='none';
confirmOrder.onclick=()=>{
  const cash=+cashPay.value||0, pos=+posPay.value||0, bank=+bankPay.value||0;
  const paid = cash+pos+bank;
  const total = cart.reduce((a,p)=>a+p.price*p.qty,0);
  if(paid<total) return alert("Incomplete payment");
  cart.forEach(c=>{
    const idx=productData.findIndex(p=>p.name===c.name);
    if(idx!==-1) productData[idx].qty -= c.qty;
    soldData.push({...c,payment:{cash,pos,bank}});
  });
  cart=[]; saveLocal(); renderProducts(); updateCartUI(); checkoutModal.style.display='none'; alert("Sale completed");
}

/* ==============================
   DISABLED USER CHECK
============================== */

setInterval(() => {
  const user = auth.currentUser;
  if (!user) return;

  user.getIdToken(true).catch(err => {
    if (err.code === "auth/user-disabled") {
      auth.signOut().then(() => {
        alert("Account disabled. Logged out.");
        window.location.href = "firebase login.html";
      });
    }
  });
}, 5000); // every 5 seconds
function setAuthIcon(loggedIn) {
  const btn = document.getElementById("authBtn");
  btn.innerText = loggedIn ? "🔓" : "🔑";
  
  btn.onclick = () => {
    if (loggedIn) {
      auth.signOut().then(() => {
        window.location.href = "firebase login.html";
      });
    } else {
      window.location.href = "firebase login.html";
    }
  };
}
/* =====================================================
   INITIAL RENDER
===================================================== */
renderProducts(); updateCartUI();
</script>
</body>
</html>

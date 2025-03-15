// Toggle between sections (Users, Products, Orders)
function toggleSection(sectionId) {
    const sections = document.querySelectorAll('.section');
    sections.forEach((section) => {
        section.classList.remove('active'); // Hide all sections
    });
    
    const activeSection = document.getElementById(sectionId);
    if (activeSection) {
        activeSection.classList.add('active'); // Show the selected section
    }
}

// View User Details
function viewUserDetails(userId) {
    fetch(`get_user_details.php?id=${userId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const userDetails = data.user;
                alert(`Username: ${userDetails.username}\nEmail: ${userDetails.email}\nJoined: ${userDetails.created_at}`);
            } else {
                alert("User details not found.");
            }
        })
        .catch(error => {
            console.error("Error fetching user details:", error);
            alert("Error fetching user details.");
        });
}

// View Product Details
function viewProductDetails(productId) {
    fetch(`get_product_details.php?id=${productId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const productDetails = data.product;
                alert(`Name: ${productDetails.name}\nPrice: $${productDetails.price}\nCategory: ${productDetails.category}\nDescription: ${productDetails.description}`);
            } else {
                alert("Product details not found.");
            }
        })
        .catch(error => {
            console.error("Error fetching product details:", error);
            alert("Error fetching product details.");
        });
}

// View Order Details
function viewOrderDetails(orderId) {
    fetch(`get_order_details.php?id=${orderId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const orderDetails = data.order;
                alert(`Order ID: ${orderDetails.order_id}\nUser ID: ${orderDetails.user_id}\nProduct: ${orderDetails.product_name}\nQuantity: ${orderDetails.quantity}\nTotal Amount: $${orderDetails.total_amount}\nPayment Status: ${orderDetails.payment_status}\nOrder Date: ${orderDetails.order_date}`);
            } else {
                alert("Order details not found.");
            }
        })
        .catch(error => {
            console.error("Error fetching order details:", error);
            alert("Error fetching order details.");
        });
}

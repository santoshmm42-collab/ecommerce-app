// Custom JavaScript for E-Commerce Website

$(document).ready(function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Back to top button
    $(window).scroll(function() {
        if ($(this).scrollTop() > 300) {
            $('#backToTop').addClass('show');
        } else {
            $('#backToTop').removeClass('show');
        }
    });

    $('#backToTop').click(function() {
        $('html, body').animate({scrollTop: 0}, 800);
        return false;
    });

    // Add to cart functionality
    $('.add-to-cart').click(function(e) {
        e.preventDefault();
        var productId = $(this).data('product-id');
        var quantity = $(this).data('quantity') || 1;
        
        $.ajax({
            url: 'cart.php',
            type: 'POST',
            data: {
                action: 'add',
                product_id: productId,
                quantity: quantity
            },
            success: function(response) {
                var data = JSON.parse(response);
                if (data.success) {
                    updateCartCount(data.cart_count);
                    showNotification('Product added to cart!', 'success');
                } else {
                    showNotification(data.message, 'error');
                }
            },
            error: function() {
                showNotification('Error adding product to cart', 'error');
            }
        });
    });

    // Update cart count
    function updateCartCount(count) {
        $('#cart-count').text(count);
    }

    // Show notification
    function showNotification(message, type) {
        var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        var notification = $('<div class="alert ' + alertClass + ' alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3" style="z-index: 9999;">' +
            message +
            '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
            '</div>');
        
        $('body').append(notification);
        
        setTimeout(function() {
            notification.alert('close');
        }, 3000);
    }

    // Product image carousel
    if ($('#productCarousel').length) {
        $('#productCarousel').carousel({
            interval: 5000
        });
    }

    // Hero slider
    if ($('#heroSlider').length) {
        $('#heroSlider').carousel({
            interval: 6000,
            pause: 'hover'
        });
    }

    // Form validation
    $('form').submit(function(e) {
        var form = $(this);
        var isValid = true;
        
        form.find('input[required], textarea[required]').each(function() {
            if (!$(this).val()) {
                isValid = false;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            showNotification('Please fill in all required fields', 'error');
        }
    });

    // Quantity selector
    $('.quantity-selector').on('click', function() {
        var input = $(this).siblings('input[type="number"]');
        var currentValue = parseInt(input.val());
        var action = $(this).data('action');
        
        if (action === 'increase') {
            input.val(currentValue + 1);
        } else if (action === 'decrease' && currentValue > 1) {
            input.val(currentValue - 1);
        }
    });

    // Remove from cart
    $('.remove-from-cart').click(function(e) {
        e.preventDefault();
        var productId = $(this).data('product-id');
        
        if (confirm('Are you sure you want to remove this item from cart?')) {
            $.ajax({
                url: 'cart.php',
                type: 'POST',
                data: {
                    action: 'remove',
                    product_id: productId
                },
                success: function(response) {
                    var data = JSON.parse(response);
                    if (data.success) {
                        location.reload();
                    } else {
                        showNotification(data.message, 'error');
                    }
                },
                error: function() {
                    showNotification('Error removing item from cart', 'error');
                }
            });
        }
    });

    // Update cart quantity
    $('.update-quantity').change(function() {
        var productId = $(this).data('product-id');
        var quantity = $(this).val();
        
        $.ajax({
            url: 'cart.php',
            type: 'POST',
            data: {
                action: 'update',
                product_id: productId,
                quantity: quantity
            },
            success: function(response) {
                var data = JSON.parse(response);
                if (data.success) {
                    location.reload();
                } else {
                    showNotification(data.message, 'error');
                }
            },
            error: function() {
                showNotification('Error updating cart', 'error');
            }
        });
    });

    // Category filter
    $('.category-filter').change(function() {
        var categoryId = $(this).val();
        var url = new URL(window.location.href);
        url.searchParams.set('category', categoryId);
        window.location.href = url.toString();
    });

    // Sort products
    $('.sort-products').change(function() {
        var sortBy = $(this).val();
        var url = new URL(window.location.href);
        url.searchParams.set('sort', sortBy);
        window.location.href = url.toString();
    });

    // Price range slider
    if ($('#priceRange').length) {
        $('#priceRange').slider({
            range: true,
            min: 0,
            max: 1000,
            values: [0, 500],
            slide: function(event, ui) {
                $('#minPrice').val(ui.values[0]);
                $('#maxPrice').val(ui.values[1]);
            }
        });
    }

    // Product search with autocomplete
    $('#productSearch').autocomplete({
        source: function(request, response) {
            $.ajax({
                url: 'search.php',
                type: 'GET',
                data: {
                    term: request.term
                },
                success: function(data) {
                    response(JSON.parse(data));
                }
            });
        },
        minLength: 2
    });

    // Review form
    $('#reviewForm').submit(function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        
        $.ajax({
            url: 'submit_review.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                var data = JSON.parse(response);
                if (data.success) {
                    showNotification('Review submitted successfully!', 'success');
                    $('#reviewForm')[0].reset();
                    location.reload();
                } else {
                    showNotification(data.message, 'error');
                }
            },
            error: function() {
                showNotification('Error submitting review', 'error');
            }
        });
    });

    // Newsletter subscription
    $('#newsletterForm').submit(function(e) {
        e.preventDefault();
        var email = $(this).find('input[type="email"]').val();
        
        $.ajax({
            url: 'newsletter.php',
            type: 'POST',
            data: {
                email: email
            },
            success: function(response) {
                var data = JSON.parse(response);
                if (data.success) {
                    showNotification('Successfully subscribed to newsletter!', 'success');
                    $('#newsletterForm')[0].reset();
                } else {
                    showNotification(data.message, 'error');
                }
            },
            error: function() {
                showNotification('Error subscribing to newsletter', 'error');
            }
        });
    });

    // Lazy loading for images
    $('img[data-src]').each(function() {
        var $img = $(this);
        $img.attr('src', $img.data('src'));
        $img.removeAttr('data-src');
    });

    // Initialize animations on scroll
    $(window).scroll(function() {
        $('.fade-in').each(function() {
            var bottom_of_element = $(this).offset().top + $(this).outerHeight();
            var bottom_of_window = $(window).scrollTop() + $(window).height();
            
            if (bottom_of_window > bottom_of_element) {
                $(this).addClass('animated');
            }
        });
    });

    // Product wishlist
    $('.add-to-wishlist').click(function(e) {
        e.preventDefault();
        var productId = $(this).data('product-id');
        var $button = $(this);
        
        $.ajax({
            url: 'wishlist.php',
            type: 'POST',
            data: {
                action: 'add',
                product_id: productId
            },
            success: function(response) {
                var data = JSON.parse(response);
                if (data.success) {
                    $button.find('i').toggleClass('far fas');
                    showNotification('Product added to wishlist!', 'success');
                } else {
                    showNotification(data.message, 'error');
                }
            },
            error: function() {
                showNotification('Error adding to wishlist', 'error');
            }
        });
    });

    // Compare products
    $('.add-to-compare').click(function(e) {
        e.preventDefault();
        var productId = $(this).data('product-id');
        
        $.ajax({
            url: 'compare.php',
            type: 'POST',
            data: {
                product_id: productId
            },
            success: function(response) {
                var data = JSON.parse(response);
                if (data.success) {
                    showNotification('Product added to comparison!', 'success');
                } else {
                    showNotification(data.message, 'error');
                }
            },
            error: function() {
                showNotification('Error adding to comparison', 'error');
            }
        });
    });

    // Quick view modal
    $('.quick-view').click(function(e) {
        e.preventDefault();
        var productId = $(this).data('product-id');
        
        $.ajax({
            url: 'quick_view.php',
            type: 'GET',
            data: {
                product_id: productId
            },
            success: function(response) {
                $('#quickViewModal .modal-body').html(response);
                $('#quickViewModal').modal('show');
            },
            error: function() {
                showNotification('Error loading product details', 'error');
            }
        });
    });
});

// Admin dashboard functions
function loadDashboardStats() {
    $.ajax({
        url: 'admin/api/dashboard_stats.php',
        type: 'GET',
        success: function(data) {
            var stats = JSON.parse(data);
            $('#totalUsers').text(stats.total_users);
            $('#totalProducts').text(stats.total_products);
            $('#totalOrders').text(stats.total_orders);
            $('#totalRevenue').text('$' + stats.total_revenue);
            
            // Update sales chart
            updateSalesChart(stats.sales_data);
        },
        error: function() {
            console.error('Error loading dashboard stats');
        }
    });
}

function updateSalesChart(salesData) {
    var ctx = document.getElementById('salesChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: salesData.labels,
            datasets: [{
                label: 'Sales',
                data: salesData.values,
                borderColor: '#3498db',
                backgroundColor: 'rgba(52, 152, 219, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

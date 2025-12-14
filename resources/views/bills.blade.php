@extends('layouts.mainLayout')

@section('title', 'My Payments - TravelCare')

@section('css')
<link rel="stylesheet" href="{{ asset('css/bills.css') }}">
@endsection

@section('content')
<div class="bills-container">
    <div class="bills-content">
        <!-- Payments History Section -->
        <div class="payments-section">
            <div class="payments-header">
                <h2 class="payments-title">My Payments</h2>
                <div class="balance-info">
                    <span class="balance-text">Balance: <strong>$2,596.45</strong></span>
                    <button class="withdraw-btn">Withdraw</button>
                </div>
            </div>
            
            <div class="payments-list">
                <!-- Today -->
                <div class="payment-date-group">
                    <h3 class="payment-date">Today</h3>
                    
                    <div class="payment-item" data-invoice="1001" data-date="13.08.2023" data-recipient="Martha Zoldana" data-service="House Visit" data-amount="400" data-charges="20">
                        <div class="payment-info">
                            <h4 class="payment-service">House Visit</h4>
                            <p class="payment-client">Madelyn Rosser</p>
                        </div>
                    </div>
                    
                    <div class="payment-item" data-invoice="1002" data-date="13.08.2023" data-recipient="John Doe" data-service="Messaging" data-amount="300" data-charges="15">
                        <div class="payment-info">
                            <h4 class="payment-service">Messaging</h4>
                            <p class="payment-client">Mira Siphron</p>
                        </div>
                    </div>
                    
                    <div class="payment-item" data-invoice="1003" data-date="13.08.2023" data-recipient="Sarah Wilson" data-service="Voice Call" data-amount="300" data-charges="15">
                        <div class="payment-info">
                            <h4 class="payment-service">Voice Call</h4>
                            <p class="payment-client">Arina Franco</p>
                        </div>
                    </div>
                    
                    <div class="payment-item" data-invoice="1004" data-date="13.08.2023" data-recipient="Michael Brown" data-service="Voice Call" data-amount="300" data-charges="15">
                        <div class="payment-info">
                            <h4 class="payment-service">Voice Call</h4>
                            <p class="payment-client">Jordyn Levin</p>
                        </div>
                    </div>
                </div>
                
                <!-- 7 July, 2023 -->
                <div class="payment-date-group">
                    <h3 class="payment-date">7 July, 2023</h3>
                    
                    <div class="payment-item" data-invoice="1005" data-date="07.07.2023" data-recipient="Emily Davis" data-service="House Visit" data-amount="500" data-charges="25">
                        <div class="payment-info">
                            <h4 class="payment-service">House Visit</h4>
                            <p class="payment-client">Allison Donin</p>
                        </div>
                    </div>
                </div>
                
                <!-- 1 July, 2023 -->
                <div class="payment-date-group">
                    <h3 class="payment-date">1 July, 2023</h3>
                    
                    <div class="payment-item" data-invoice="1006" data-date="01.07.2023" data-recipient="David Wilson" data-service="Voice Call" data-amount="300" data-charges="15">
                        <div class="payment-info">
                            <h4 class="payment-service">Voice Call</h4>
                            <p class="payment-client">Ashlynn Curtis</p>
                        </div>
                    </div>
                    
                    <div class="payment-item" data-invoice="1007" data-date="01.07.2023" data-recipient="Lisa Johnson" data-service="Messaging" data-amount="250" data-charges="12">
                        <div class="payment-info">
                            <h4 class="payment-service">Messaging</h4>
                            <p class="payment-client">Mira Franco</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Pagination -->
            <div class="pagination-wrapper">
                <div class="pagination">
                    <button class="pagination-btn prev" disabled>
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>
                    <button class="pagination-btn active">1</button>
                    <button class="pagination-btn">2</button>
                    <button class="pagination-btn">3</button>
                    <button class="pagination-btn">4</button>
                    <button class="pagination-btn">5</button>
                    <button class="pagination-btn">6</button>
                    <button class="pagination-btn">7</button>
                    <button class="pagination-btn">8</button>
                    <button class="pagination-btn">9</button>
                    <button class="pagination-btn">10</button>
                    <button class="pagination-btn">11</button>
                    <button class="pagination-btn">12</button>
                    <button class="pagination-btn">13</button>
                    <button class="pagination-btn">14</button>
                    <button class="pagination-btn next">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Invoice Details Section -->
        <div class="invoice-section">
            <div class="invoice-details">
                <div class="detail-row">
                    <span class="detail-label">Invoice Number</span>
                    <span class="detail-value" id="invoiceNumber">1001</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Date</span>
                    <span class="detail-value" id="invoiceDate">13.08.2023</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Recipient Name</span>
                    <span class="detail-value" id="recipientName">Martha Zoldana</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Name Of The Service</span>
                    <span class="detail-value" id="serviceName">House Visit</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Total Amount</span>
                    <span class="detail-value" id="totalAmount">$400</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Platform Charges And Taxes</span>
                    <span class="detail-value" id="platformCharges">$20</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Invoice Modal for Tablet/Mobile -->
<div id="invoiceModal" class="invoice-modal">
    <div class="invoice-modal-content">
        <div class="invoice-modal-header">
            <h3 class="invoice-modal-title">Invoice Details</h3>
            <button class="invoice-modal-close" id="closeInvoiceModal">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        
        <div class="invoice-modal-body">
            <div class="invoice-details">
                <div class="detail-row">
                    <span class="detail-label">Invoice Number</span>
                    <span class="detail-value" id="modalInvoiceNumber">1001</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Date</span>
                    <span class="detail-value" id="modalInvoiceDate">13.08.2023</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Recipient Name</span>
                    <span class="detail-value" id="modalRecipientName">Martha Zoldana</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Name Of The Service</span>
                    <span class="detail-value" id="modalServiceName">House Visit</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Total Amount</span>
                    <span class="detail-value" id="modalTotalAmount">$400</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Platform Charges And Taxes</span>
                    <span class="detail-value" id="modalPlatformCharges">$20</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ asset('js/bills.js') }}"></script>
@endsection
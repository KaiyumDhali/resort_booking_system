@extends('pages.frontend.layouts.app')


@section('content')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root{
            --room-ink:#16231F;
            --room-ivory:#FBF8F3;
            --room-gold:#B68A4E;
            --room-gold-soft:rgba(182,138,78,.12);
            --room-sage:#E7ECE4;
            --room-muted:#5B6760;
            --room-border:#E7E2D8;
            --room-danger:#B3452F;
            --room-danger-bg:#FBEEE9;
            --room-radius-lg:20px;
            --room-radius-md:14px;
            --room-radius-sm:10px;
            --room-shadow:0 18px 40px rgba(22,35,31,.08);
            --font-display:'Playfair Display', Georgia, serif;
            --font-body:'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        .room-details-section{
            font-family: var(--font-body);
        }
        .room-details-section :where(h1,h2,h3,h4,h5,h6,p,span,div,a,button,input,label,li,td,th){
            font-family: var(--font-body);
        }
        .room-details-section i.fa-solid,
        .custom-modal i.fa-solid{
            font-family: "Font Awesome 6 Free" !important;
            font-weight: 900 !important;
        }
        .room-details-section i.fa-brands,
        .custom-modal i.fa-brands{
            font-family: "Font Awesome 6 Brands" !important;
            font-weight: 400 !important;
        }
        .room-details-section h1,
        .room-details-section h2,
        .room-details-section h3,
        .room-details-section h4,
        .room-details-section .sec-title,
        .room-page-title h1,
        .booking-summary-card h3,
        #bookingModal .modal-title{
            font-family: var(--font-display);
            letter-spacing:.01em;
        }

        /* ============== PAGE TITLE ============== */
        .room-page-title{
            position:relative;
            min-height:320px;
            display:flex;
            align-items:center;
            justify-content:center;
            background-size:cover;
            background-position:center;
        }
        .room-page-title::before{
            content:"";
            position:absolute;
            inset:0;
            background:linear-gradient(180deg, rgba(22,35,31,.35), rgba(22,35,31,.75));
        }
        .room-page-title .auto-container{ position:relative; z-index:2; }
        .room-page-title .eyebrow{
            display:inline-flex;
            align-items:center;
            gap:8px;
            color:var(--room-gold);
            letter-spacing:.18em;
            text-transform:uppercase;
            font-size:12.5px;
            font-weight:700;
            margin-bottom:14px;
        }
        .room-page-title .eyebrow::before,
        .room-page-title .eyebrow::after{
            content:"";
            width:24px;height:1px;
            background:var(--room-gold);
            display:inline-block;
        }
        .room-page-title h1{
            color:#fff;
            font-weight:600;
            font-size:44px;
            margin:0;
        }

        /* ============== ROOM DETAILS SECTION ============== */
        .room-details-section{ background:var(--room-ivory) !important; padding-top:70px; padding-bottom:90px; }

        .room-gallery-shell{
            border-radius:var(--room-radius-lg);
            overflow:hidden;
            box-shadow:var(--room-shadow);
            margin-bottom:44px;
            background:#fff;
            border:1px solid var(--room-border);
        }
        .single-item-with-pager-carousel{ overflow:hidden; }
        .single-item-with-pager-carousel img{
            width:100%;
            display:block;
            object-fit:cover;
            max-height:560px;
            filter:saturate(1.05) contrast(1.02);
            transition:transform .5s cubic-bezier(.22,.61,.36,1);
        }
        .single-item-with-pager-carousel .swiper-slide:hover img{
            transform:scale(1.035);
        }
        .single-item-with-pager-thumb{ padding:16px; background:#fff; }
        .single-item-with-pager-thumb .thumb{
            border-radius:var(--room-radius-sm);
            overflow:hidden;
            border:2px solid transparent;
            cursor:pointer;
            transition:.25s ease;
        }
        .single-item-with-pager-thumb .thumb:hover{
            border-color:rgba(182,138,78,.5);
        }
        .single-item-with-pager-thumb .swiper-slide-thumb-active .thumb{
            border-color:var(--room-gold);
            box-shadow:0 6px 16px rgba(182,138,78,.28);
        }
        .single-item-with-pager-thumb img{ width:100%; display:block; aspect-ratio:4/3; object-fit:cover; }

        /* Room info block */
        .room-info-card{
            background:#fff;
            border:1px solid var(--room-border);
            border-radius:var(--room-radius-lg);
            padding:36px 38px;
            box-shadow:var(--room-shadow);
            margin-bottom:32px;
        }
        .room-info-card .pricing{
            display:inline-block;
            background:var(--room-ink);
            color:var(--room-gold);
            font-weight:700;
            font-size:15px;
            padding:9px 20px;
            border-radius:999px;
            margin-bottom:20px;
            letter-spacing:.03em;
        }
        .room-info-card .sec-title{
            font-weight:600;
            font-size:26px;
            color:var(--room-ink);
            margin-bottom:18px;
            position:relative;
            padding-bottom:18px;
        }
        .room-info-card .sec-title::after{
            content:"";
            position:absolute;
            left:0;bottom:0;
            width:54px;height:3px;
            background:var(--room-gold);
            border-radius:3px;
        }
        .room-info-card .text{
            color:var(--room-muted);
            line-height:1.9;
            font-size:15.5px;
        }

        /* Facilities / amenities grid */
        .facilities-card{
            background:#fff;
            border:1px solid var(--room-border);
            border-radius:var(--room-radius-lg);
            padding:36px 38px;
            box-shadow:var(--room-shadow);
        }
        .facilities-card .sec-title{
            font-weight:600;
            font-size:22px;
            color:var(--room-ink);
            margin-bottom:24px;
            position:relative;
            padding-bottom:16px;
        }
        .facilities-card .sec-title::after{
            content:"";
            position:absolute;
            left:0;bottom:0;
            width:54px;height:3px;
            background:var(--room-gold);
            border-radius:3px;
        }
        .facilities-grid{
            display:grid;
            grid-template-columns:repeat(auto-fill, minmax(150px, 1fr));
            gap:16px;
            margin-bottom:8px;
        }
        .facility-item{
            display:flex;
            flex-direction:column;
            align-items:center;
            text-align:center;
            gap:12px;
            padding:22px 12px;
            border:1px solid var(--room-border);
            border-radius:var(--room-radius-md);
            background:var(--room-ivory);
            transition:.25s ease;
        }
        .facility-item:hover{
            border-color:var(--room-gold);
            background:#fff;
            box-shadow:0 12px 24px rgba(22,35,31,.08);
            transform:translateY(-3px);
        }
        .facility-item .ico{
            width:48px;
            height:48px;
            display:flex;
            align-items:center;
            justify-content:center;
            border-radius:50%;
            background:var(--room-gold-soft);
            color:var(--room-gold);
            font-size:19px;
        }
        .facility-item .lbl{
            font-size:13.5px;
            font-weight:600;
            color:var(--room-ink);
            line-height:1.3;
        }

        /* Divider between content blocks */
        .room-block-divider{
            border:none;
            border-top:1px dashed var(--room-border);
            margin:36px 0;
        }

        /* Sub-section heading style (House Rules / Pets etc.) */
        .room-sub-title{
            font-family: var(--font-display);
            font-weight:600;
            font-size:20px;
            color:var(--room-ink);
            margin-bottom:20px;
            display:flex;
            align-items:center;
            gap:10px;
        }
        .room-sub-title::before{
            content:"";
            width:8px;height:8px;
            border-radius:50%;
            background:var(--room-gold);
            display:inline-block;
        }

        /* House rules cards */
        .house-rule-block{
            background:var(--room-ivory);
            border:1px solid var(--room-border);
            border-radius:var(--room-radius-md);
            padding:20px 22px;
            height:100%;
        }
        .house-rule-block .check{
            text-transform:uppercase;
            letter-spacing:.1em;
            font-size:11.5px;
            font-weight:700;
            color:var(--room-gold);
            margin-bottom:8px;
        }
        .house-rule-block .time{
            font-weight:600;
            color:var(--room-ink);
            font-size:15px;
            margin-bottom:6px;
        }
        .house-rule-block .subject,
        .house-rule-block .age{
            color:var(--room-muted);
            font-size:13.5px;
            line-height:1.6;
        }

        /* Pets text */
        .pets-value{
            display:inline-flex;
            align-items:center;
            gap:8px;
            font-weight:600;
            color:var(--room-ink);
            background:var(--room-ivory);
            border:1px solid var(--room-border);
            border-radius:999px;
            padding:8px 16px;
            font-size:14px;
        }
        .pets-value i{ color:var(--room-gold); }

        /* Amenities list */
        .list-one{
            list-style:none;
            margin:0;
            padding:0;
        }
        .list-one li{
            display:flex;
            align-items:center;
            gap:10px;
            font-size:14.5px;
            color:var(--room-ink);
            font-weight:500;
            padding:9px 0;
            border-bottom:1px dashed var(--room-border);
        }
        .list-one li:last-child{ border-bottom:none; }
        .list-one li i{
            color:var(--room-gold);
            width:20px;
            text-align:center;
            font-size:16px;
        }

        /* ============== NID NOTICE ============== */
        .nid-alert{
            display:flex;
            align-items:flex-start;
            gap:14px;
            background:var(--room-danger-bg);
            border:1px solid rgba(179,69,47,.25);
            border-radius:var(--room-radius-md);
            padding:16px 18px;
            margin-top:24px;
        }
        .nid-alert .ico{
            width:40px;
            height:40px;
            flex:0 0 40px;
            border-radius:50%;
            background:#fff;
            color:var(--room-danger);
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:17px;
            border:1px solid rgba(179,69,47,.25);
        }
        .nid-alert .body p{
            margin:0;
            font-size:14px;
            line-height:1.6;
            color:#5c3226;
        }
        .nid-alert .body p strong{ color:var(--room-danger); }
        .nid-alert .body .bn{
            display:block;
            margin-top:4px;
            font-size:13px;
            color:#7a4a3a;
        }

        /* ============== BOOKING SUMMARY CARD (triggers modal) ============== */
        .booking-keycard-wrap{ position:sticky; top:24px; }

        .booking-summary-card{
            background:#fff;
            border:1px solid var(--room-border);
            border-radius:var(--room-radius-lg);
            box-shadow:var(--room-shadow);
            overflow:hidden;
            position:relative;
        }
        .booking-summary-card::before{
            content:"";
            position:absolute;
            top:0;left:0;right:0;
            height:5px;
            background:linear-gradient(90deg,var(--room-gold),#d8b074,var(--room-gold));
        }
        .booking-summary-card .bs-head{
            padding:28px 28px 20px;
            border-bottom:1px dashed var(--room-border);
        }
        .booking-summary-card .bs-eyebrow{
            text-transform:uppercase;
            letter-spacing:.14em;
            font-size:11px;
            font-weight:700;
            color:var(--room-gold);
            margin-bottom:8px;
        }
        .booking-summary-card h3{
            margin:0 0 8px;
            font-weight:600;
            font-size:24px;
            color:var(--room-ink);
        }
        .booking-summary-card .bs-price{
            font-size:23px;
            font-weight:700;
            color:var(--room-ink);
            font-family:var(--font-body);
        }
        .booking-summary-card .bs-price span{
            font-size:13px;
            font-weight:500;
            color:var(--room-muted);
        }
        .booking-summary-card .bs-body{ padding:24px 28px 30px; }

        .bs-quick-facts{
            display:flex;
            flex-wrap:wrap;
            gap:10px;
            margin-bottom:24px;
        }
        .bs-quick-facts .fact{
            display:flex;
            align-items:center;
            gap:7px;
            font-size:12.5px;
            font-weight:600;
            color:var(--room-ink);
            background:var(--room-ivory);
            border:1px solid var(--room-border);
            border-radius:999px;
            padding:7px 13px;
        }
        .bs-quick-facts .fact i{ color:var(--room-gold); }

        .booking-summary-card .theme-btn,
        .room-info-card .theme-btn{
            background:linear-gradient(135deg,var(--room-ink),#23362f);
            border:none;
            border-radius:var(--room-radius-sm);
            padding:15px 22px;
            font-weight:700;
            letter-spacing:.02em;
            box-shadow:0 14px 28px rgba(22,35,31,.22);
            transition:.25s cubic-bezier(.22,.61,.36,1);
            border:1px solid transparent;
        }
        .booking-summary-card .theme-btn:hover,
        .room-info-card .theme-btn:hover{
            transform:translateY(-2px);
            box-shadow:0 20px 34px rgba(22,35,31,.3);
            color:var(--room-gold);
            border-color:rgba(182,138,78,.4);
        }

        .ca-note{
            display:flex;
            align-items:center;
            gap:8px;
            font-size:12.5px;
            color:var(--room-muted);
            background:var(--room-sage);
            border-radius:var(--room-radius-sm);
            padding:10px 12px;
            margin-top:16px;
        }
        .ca-note i{ color:var(--room-gold); }

        .ca-note-nid{
            display:flex;
            align-items:center;
            gap:8px;
            font-size:12.5px;
            font-weight:600;
            color:var(--room-danger);
            background:var(--room-danger-bg);
            border:1px solid rgba(179,69,47,.2);
            border-radius:var(--room-radius-sm);
            padding:10px 12px;
            margin-top:10px;
        }
        .ca-note-nid i{ color:var(--room-danger); }

        @media (max-width: 991px){
            .booking-keycard-wrap{ position:static; margin-top:32px; }
        }

        /* ============== SIDEBAR: TRUST BADGES ============== */
        .trust-badges-card{
            background:#fff;
            border:1px solid var(--room-border);
            border-radius:var(--room-radius-lg);
            box-shadow:var(--room-shadow);
            padding:22px 24px;
            margin-top:20px;
        }
        .trust-item{
            display:flex;
            align-items:center;
            gap:14px;
            padding:12px 0;
            border-bottom:1px dashed var(--room-border);
        }
        .trust-item:last-child{ border-bottom:none; }
        .trust-item .ico{
            width:38px;
            height:38px;
            flex:0 0 38px;
            border-radius:50%;
            background:var(--room-gold-soft);
            color:var(--room-gold);
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:15px;
        }
        .trust-item .txt strong{
            display:block;
            font-size:13.5px;
            font-weight:700;
            color:var(--room-ink);
        }
        .trust-item .txt span{
            display:block;
            font-size:12px;
            color:var(--room-muted);
            margin-top:2px;
            line-height:1.4;
        }

        /* ============== SIDEBAR: POLICY CARD ============== */
        .policy-card{
            background:#fff;
            border:1px solid var(--room-border);
            border-radius:var(--room-radius-lg);
            box-shadow:var(--room-shadow);
            padding:24px 26px;
            margin-top:20px;
        }
        .policy-card .sc-title{
            font-family: var(--font-display);
            font-weight:600;
            font-size:17px;
            color:var(--room-ink);
            margin-bottom:14px;
            display:flex;
            align-items:center;
            gap:9px;
        }
        .policy-card .sc-title i{ color:var(--room-gold); font-size:15px; }
        .policy-list{
            list-style:none;
            margin:0;
            padding:0;
        }
        .policy-list li{
            display:flex;
            align-items:flex-start;
            gap:10px;
            font-size:13px;
            color:var(--room-muted);
            line-height:1.6;
            padding:7px 0;
        }
        .policy-list li i{
            color:var(--room-gold);
            font-size:12px;
            margin-top:4px;
            flex:0 0 14px;
        }
        .policy-list li strong{ color:var(--room-ink); font-weight:600; }

        /* ============== SIDEBAR: HELP / CONTACT CARD ============== */
        .help-card{
            background:linear-gradient(135deg,var(--room-ink),#23362f);
            border-radius:var(--room-radius-lg);
            box-shadow:var(--room-shadow);
            padding:26px 26px;
            margin-top:20px;
            position:relative;
            overflow:hidden;
        }
        .help-card::before{
            content:"";
            position:absolute;
            top:0;left:0;right:0;
            height:4px;
            background:linear-gradient(90deg,var(--room-gold),#d8b074,var(--room-gold));
        }
        .help-card .sc-title{
            font-family: var(--font-display);
            font-weight:600;
            font-size:17px;
            color:#fff;
            margin-bottom:8px;
        }
        .help-card p{
            font-size:13px;
            color:rgba(255,255,255,.72);
            line-height:1.6;
            margin-bottom:18px;
        }
        .help-card .help-contact{
            display:flex;
            align-items:center;
            gap:12px;
            background:rgba(255,255,255,.08);
            border:1px solid rgba(255,255,255,.14);
            border-radius:var(--room-radius-sm);
            padding:12px 14px;
            text-decoration:none;
            transition:.2s;
        }
        .help-card .help-contact:hover{
            background:rgba(255,255,255,.14);
        }
        .help-card .help-contact .ico{
            width:36px;
            height:36px;
            flex:0 0 36px;
            border-radius:50%;
            background:var(--room-gold);
            color:var(--room-ink);
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:14px;
        }
        .help-card .help-contact .txt strong{
            display:block;
            font-size:14px;
            font-weight:700;
            color:#fff;
        }
        .help-card .help-contact .txt span{
            display:block;
            font-size:11.5px;
            color:rgba(255,255,255,.6);
            margin-top:1px;
        }

        /* ============== BOOKING MODAL (framework-independent, no Bootstrap JS needed) ============== */
        body.room-modal-lock{ overflow:hidden; }

        .custom-modal{
            position:fixed;
            inset:0;
            z-index:9999;
            display:none;
            overflow-y:auto;
            padding:40px 16px;
            -webkit-overflow-scrolling:touch;
            font-family: var(--font-body);
        }
        .custom-modal.is-open{ display:block; }
        .custom-modal .custom-modal-overlay{
            position:fixed;
            inset:0;
            background:rgba(22,35,31,.6);
            backdrop-filter:blur(2px);
            z-index:0;
        }
        .custom-modal .custom-modal-dialog{
            position:relative;
            z-index:1;
            max-width:640px;
            width:100%;
            margin:0 auto;
        }
        .custom-modal .custom-modal-content{
            border:none;
            border-radius:var(--room-radius-lg);
            overflow:hidden;
            box-shadow:0 30px 70px rgba(22,35,31,.3);
            background:#fff;
            animation:roomModalPop .22s cubic-bezier(.22,.61,.36,1);
        }
        @keyframes roomModalPop{
            from{ opacity:0; transform:translateY(14px) scale(.97); }
            to{ opacity:1; transform:translateY(0) scale(1); }
        }
        #bookingModal .modal-header{
            background:linear-gradient(135deg,var(--room-ink),#23362f);
            border:none;
            padding:24px 28px;
            position:relative;
            display:flex;
            align-items:flex-start;
            justify-content:space-between;
            gap:16px;
        }
        #bookingModal .modal-header::after{
            content:"";
            position:absolute;
            bottom:0;left:0;right:0;
            height:4px;
            background:linear-gradient(90deg,var(--room-gold),#d8b074,var(--room-gold));
        }
        #bookingModal .modal-header .modal-eyebrow{
            text-transform:uppercase;
            letter-spacing:.14em;
            font-size:11px;
            font-weight:700;
            color:var(--room-gold);
            margin-bottom:6px;
        }
        #bookingModal .modal-title{
            color:#fff;
            font-weight:600;
            font-size:22px;
            margin:0;
        }
        #bookingModal .custom-modal-close{
            background:rgba(255,255,255,.12);
            border:none;
            color:#fff;
            width:32px;
            height:32px;
            border-radius:50%;
            font-size:18px;
            line-height:1;
            cursor:pointer;
            flex:0 0 32px;
            transition:.2s;
        }
        #bookingModal .custom-modal-close:hover{ background:rgba(255,255,255,.22); }
        #bookingModal .modal-body{ padding:28px; background:var(--room-ivory); }

        #bookingModal .check-availability form ul{
            list-style:none;
            margin:0;
            padding:0;
        }
        #bookingModal .check-availability form ul li{
            display:block;
            width:100%;
            margin-bottom:15px;
        }
        #bookingModal .date-row{
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:12px;
            margin-bottom:4px;
            list-style:none;
            padding:0;
        }
        #bookingModal .date-row li{ margin-bottom:0 !important; }
        #bookingModal .date-field{
            position:relative;
            border:1px solid var(--room-border);
            border-radius:var(--room-radius-sm);
            background:#fff;
            padding:10px 12px 8px;
            transition:.2s;
        }
        #bookingModal .date-field:focus-within{
            border-color:var(--room-gold);
            box-shadow:0 0 0 3px var(--room-gold-soft);
        }
        #bookingModal .date-field p{
            margin:0 0 2px;
            font-size:11px;
            font-weight:700;
            text-transform:uppercase;
            letter-spacing:.06em;
            color:var(--room-muted);
        }
        #bookingModal .date-field input.form-control{
            border:none;
            background:transparent;
            padding:2px 0 0;
            font-weight:600;
            color:var(--room-ink);
            font-size:15px;
        }
        #bookingModal .date-field input.form-control:focus{ outline:none; box-shadow:none; }
        #bookingModal .date-field .field-icon{
            position:absolute;
            right:10px;
            top:12px;
            color:var(--room-gold);
            font-size:14px;
            pointer-events:none;
        }
        #bookingModal .guest-field{ margin-top:16px; }
        #bookingModal .guest-field p{
            font-size:12.5px;
            font-weight:700;
            color:var(--room-ink);
            margin-bottom:6px;
        }
        #bookingModal .guest-field input.form-control{
            height:46px;
            border-radius:var(--room-radius-sm);
            border:1px solid var(--room-border);
            background:#fff;
            padding:10px 14px;
            font-size:14.5px;
            transition:.2s;
        }
        #bookingModal .guest-field input.form-control:focus{
            border-color:var(--room-gold);
            box-shadow:0 0 0 3px var(--room-gold-soft);
            outline:none;
        }
        #bookingModal .right-side{ margin-top:20px; }
        #bookingModal .right-side .theme-btn{
            background:linear-gradient(135deg,var(--room-ink),#23362f);
            border:none;
            border-radius:var(--room-radius-sm);
            padding:15px 18px;
            font-weight:700;
            letter-spacing:.02em;
            box-shadow:0 14px 28px rgba(22,35,31,.22);
            transition:.25s cubic-bezier(.22,.61,.36,1);
            border:1px solid transparent;
        }
        #bookingModal .right-side .theme-btn:hover{
            transform:translateY(-2px);
            box-shadow:0 20px 34px rgba(22,35,31,.3);
            color: var(--room-gold);
            border-color:rgba(182,138,78,.4);
        }

        /* ============== NIGHT COUNT DISPLAY ============== */
        #bookingModal .night-count-box{
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:10px;
            background:var(--room-gold-soft);
            border:1px solid rgba(182,138,78,.35);
            border-radius:var(--room-radius-sm);
            padding:10px 14px;
            margin:0 0 15px;
            font-size:13.5px;
            font-weight:700;
            color:var(--room-ink);
        }
        #bookingModal .night-count-box i{ color:var(--room-gold); margin-right:6px; }
        #bookingModal .night-count-box .nc-label{ display:flex; align-items:center; }
        #bookingModal .night-count-box .nc-value{
            background:var(--room-ink);
            color:var(--room-gold);
            border-radius:999px;
            padding:4px 12px;
            font-size:12.5px;
        }
        #bookingModal .night-count-box.is-hidden{ display:none; }

        @media (max-width: 575px){
            #bookingModal .date-row{ grid-template-columns:1fr; }
        }

        @media (max-width: 767px){
            .room-page-title h1{ font-size:32px; }
            .room-info-card, .facilities-card{ padding:26px 22px; }
        }
    </style>

    @php
        // Facility list — falls back to sensible defaults if these fields don't exist on the room model,
        // so the block always renders cleanly regardless of schema.
        $bedCount = $rooms->bed_count ?? 1;
        $guestCapacity = $rooms->capacity ?? 2;
        $roomSize = $rooms->room_size ?? null;

        $facilities = [
            ['icon' => 'fa-bed', 'label' => $bedCount.' '.($bedCount > 1 ? 'Beds' : 'Bed')],
            ['icon' => 'fa-user-group', 'label' => $guestCapacity.' Guests'],
        ];
        if ($roomSize) {
            $facilities[] = ['icon' => 'fa-ruler-combined', 'label' => $roomSize.' sq ft'];
        }
        $facilities = array_merge($facilities, [
            ['icon' => 'fa-wifi', 'label' => 'Free WiFi'],
            ['icon' => 'fa-snowflake', 'label' => 'Air Conditioned'],
            ['icon' => 'fa-tv', 'label' => 'Flat Screen TV'],
            ['icon' => 'fa-mug-hot', 'label' => 'Breakfast Included'],
            ['icon' => 'fa-shower', 'label' => 'Attached Bathroom'],
            ['icon' => 'fa-door-closed', 'label' => 'Private Balcony'],
        ]);
    @endphp

    <!-- Page Title -->
    <section class="page-title room-page-title" style="background-image: url({{ asset('storage/' . $banner_image->banner_image) }});">
        <div class="auto-container">
            <div class="text-center">
                <span class="eyebrow">Room Details</span>
                <h1>Room {{ $rooms->room_number }}</h1>
            </div>
        </div>
    </section>


    <!-- room details -->
    <section class="room-details-section light-bg mx-60 border-shape-top">
        <div class="auto-container">
            <div class="single-items-carousel room-gallery-shell">
                <!-- Swiper -->
                <div class="swiper-container single-item-with-pager-carousel">
                    <div class="swiper-wrapper">
                        @foreach ($rooms->details as $detail)
                            <div class="swiper-slide">
                                <div class="image">
                                    <img src="{{ asset(Storage::url($detail->image_path)) }}" alt="Room Image">
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="h_10 w_10"></div>
                <div class="swiper-container single-item-with-pager-thumb">
                    <div class="swiper-wrapper">
                        @foreach ($rooms->details as $detail)
                            <div class="swiper-slide">
                                <div class="thumb">
                                    <img src="{{ asset(Storage::url($detail->image_path)) }}" alt="Room Thumbnail">
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="room-info-card room-block">

                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div class="pricing px-1">
                                BDT {{ $rooms->price_per_night }} / Night
                            </div>

                            <button type="button"
                                    class="theme-btn btn-style-one btn-md"
                                    data-modal-open="bookingModal">
                                Book Now
                            </button>
                        </div>
                        <h2 class="sec-title">Room Number: {{ $rooms->room_number }}</h2>
                        <div class="text">{{ $rooms->description }}</div>
                    </div>

                    <div class="facilities-card">
                        <!-- <h3 class="sec-title">Room Facilities</h3>
                        <div class="facilities-grid">
                            @foreach ($facilities as $facility)
                                <div class="facility-item">
                                    <div class="ico"><i class="fa-solid {{ $facility['icon'] }}"></i></div>
                                    <div class="lbl">{{ $facility['label'] }}</div>
                                </div>
                            @endforeach
                        </div>

                        <hr class="room-block-divider"> -->

                        <div class="room-sub-title">Amenities</div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <ul class="list-one">
                                    <li><i class="fa-solid fa-fan"></i>Air conditioner</li>
                                    <li><i class="fa-solid fa-tv"></i>Cable TV</li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <ul class="list-one">
                                    <li><i class="fa-solid fa-door-open"></i>Balcony</li>
                                    <li><i class="fa-solid fa-bath"></i>Bathroom</li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <ul class="list-one">
                                    <li><i class="fa-solid fa-bed"></i>King size bed</li>
                                    <li><i class="fa-solid fa-wifi"></i>High speed WiFi</li>
                                </ul>
                            </div>
                        </div>

                        <hr class="room-block-divider">

                        <div class="room-sub-title">House Rules</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="house-rule-block">
                                    <div class="check">Check In</div>
                                    <div class="time">From 9:00 AM &mdash; anytime</div>
                                    <div class="subject">Early check-in subject to availability</div>
                                    <div class="age">Minimum check-in age: 18</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="house-rule-block">
                                    <div class="check">Check Out</div>
                                    <div class="time">Before noon</div>
                                    <div class="subject">Express check-out available</div>
                                </div>
                            </div>
                        </div>

                        <hr class="room-block-divider">

                        <div class="room-sub-title">Pets</div>
                        <span class="pets-value"><i class="fa-solid fa-ban"></i> Not Allowed</span>

                        <div class="nid-alert">
                            <div class="ico"><i class="fa-solid fa-id-card"></i></div>
                            <div class="body">
                                <p><strong>Please note:</strong> a valid National ID (NID) card is mandatory at check-in for every guest entering the room.</p>
                                <span class="bn">চেক-ইনের সময় রুমে প্রবেশের জন্য অবশ্যই একটি বৈধ এনআইডি কার্ড সাথে রাখতে হবে।</span>
                            </div>
                        </div>

                    </div>

                </div>

                <div class="col-lg-4">
                    <div class="booking-keycard-wrap">

                        <!-- Booking summary card — opens the booking modal -->
                        <div class="booking-summary-card">
                            <div class="bs-head">
                                <div class="bs-eyebrow">Reserve this room</div>
                                <h3>Book Your Stay</h3>
                                <div class="bs-price">BDT {{ $rooms->price_per_night }} <span>/ night</span></div>
                            </div>
                            <div class="bs-body">
                                <div class="bs-quick-facts">
                                    <span class="fact"><i class="fa-solid fa-bed"></i> {{ $bedCount }} {{ $bedCount > 1 ? 'Beds' : 'Bed' }}</span>
                                    <span class="fact"><i class="fa-solid fa-user-group"></i> {{ $guestCapacity }} Guests</span>
                                    <span class="fact"><i class="fa-solid fa-wifi"></i> WiFi</span>
                                </div>

                                <button type="button" class="theme-btn btn-style-one btn-md w-100" data-modal-open="bookingModal">
                                    Book Now
                                </button>

                                <div class="ca-note">
                                    <i class="fa fa-circle-info"></i>
                                    Dates shown in red are already booked and can't be selected.
                                </div>
                                <div class="ca-note-nid">
                                    <i class="fa-solid fa-id-card"></i>
                                    A valid NID card is required at check-in.
                                </div>
                            </div>
                        </div>

                        <!-- Trust badges -->
                        <div class="trust-badges-card">
                            <div class="trust-item">
                                <div class="ico"><i class="fa-solid fa-shield-halved"></i></div>
                                <div class="txt">
                                    <strong>Secure Booking</strong>
                                    <span>Your details are protected end-to-end</span>
                                </div>
                            </div>
                            <div class="trust-item">
                                <div class="ico"><i class="fa-solid fa-tags"></i></div>
                                <div class="txt">
                                    <strong>Best Price Guarantee</strong>
                                    <span>Book direct, no hidden charges</span>
                                </div>
                            </div>
                            <div class="trust-item">
                                <div class="ico"><i class="fa-solid fa-bolt"></i></div>
                                <div class="txt">
                                    <strong>Instant Confirmation</strong>
                                    <span>Get your booking confirmed right away</span>
                                </div>
                            </div>
                        </div>

                        <!-- Cancellation policy -->
                        <div class="policy-card">
                            <div class="sc-title"><i class="fa-solid fa-file-shield"></i> Cancellation Policy</div>
                            <ul class="policy-list">
                                <li><i class="fa-solid fa-circle-check"></i> <span><strong>Free cancellation</strong> up to 24 hours before check-in</span></li>
                                <li><i class="fa-solid fa-circle-check"></i> <span>Full refund for cancellations made in time</span></li>
                                <li><i class="fa-solid fa-circle-exclamation"></i> <span>Late cancellations or no-shows may incur a one-night charge</span></li>
                            </ul>
                        </div>

                        <!-- Need help -->
                        <div class="help-card">
                            <div class="sc-title">Need Help?</div>
                            <p>Our front desk team is available round the clock for any booking questions.</p>
                            <a href="tel:+8801XXXXXXXXX" class="help-contact">
                                <div class="ico"><i class="fa-solid fa-phone"></i></div>
                                <div class="txt">
                                    <strong>+88 01759-593166</strong>
                                    <span>Available 24/7</span>
                                </div>
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============== BOOKING MODAL (self-contained, no Bootstrap JS dependency) ============== -->
    <div class="custom-modal" id="bookingModal" aria-hidden="true">
        <div class="custom-modal-overlay" data-modal-close></div>
        <div class="custom-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="bookingModalLabel">
            <div class="custom-modal-content">
                <div class="modal-header">
                    <div>
                        <div class="modal-eyebrow">Room {{ $rooms->room_number }} &middot; BDT {{ $rooms->price_per_night }}/night</div>
                        <h5 class="modal-title" id="bookingModalLabel">Book Your Room</h5>
                    </div>
                    <button type="button" class="custom-modal-close" data-modal-close aria-label="Close">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="check-availability">
                        <form action="{{ route('book.room') }}" method="POST">
                            @csrf
                            <input type="hidden" name="room_id" value="{{ $rooms->id }}">
                            <input type="hidden" name="Booking_status" value="0">
                            <input type="hidden" name="customer_type" value="1">

                            <ul class="date-row">
                                <li>
                                    <div class="date-field">
                                        <p>Check In</p>
                                        <input type="text" id="arrival_date" name="arrival_date" placeholder="Select Date"
                                            class="form-control w-100" readonly>
                                        <i class="fa fa-calendar-day field-icon"></i>
                                    </div>
                                    @error('arrival_date')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </li>

                                <li>
                                    <div class="date-field">
                                        <p>Check Out</p>
                                        <input type="text" id="departure_date" name="departure_date"
                                            placeholder="Select Date" class="form-control w-100" readonly>
                                        <i class="fa fa-calendar-day field-icon"></i>
                                    </div>
                                    @error('departure_date')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </li>
                            </ul>

                            <!-- Night / day count — updates automatically once both dates are selected -->
                            <div class="night-count-box is-hidden" id="nightCountBox">
                                <span class="nc-label"><i class="fa-solid fa-moon"></i> Length of Stay</span>
                                <span class="nc-value" id="nightCountValue">0 Night</span>
                            </div>

                            <ul>
                                <li class="guest-field">
                                    <p>Your Name</p>
                                    <input type="text" name="customer_name" value="{{ old('customer_name') }}"
                                        class="form-control w-100" required>
                                    @error('customer_name')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </li>
                                <li class="guest-field">
                                    <p>Mobile</p>
                                    <input type="text" name="customer_phone" value="{{ old('customer_phone') }}"
                                        class="form-control w-100" required>
                                    @error('customer_phone')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </li>
                                <li class="guest-field">
                                    <p>Address</p>
                                    <input type="text" name="customer_address" value="{{ old('customer_address') }}"
                                        class="form-control w-100" required>
                                    @error('customer_address')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </li>
                            </ul>

                            <div class="nid-alert" style="margin-top:6px;">
                                <div class="ico"><i class="fa-solid fa-id-card"></i></div>
                                <div class="body">
                                    <p><strong>Reminder:</strong> please bring a valid NID card &mdash; it is required to enter the room at check-in.</p>
                                    <span class="bn">অনুগ্রহ করে চেক-ইনের সময় বৈধ এনআইডি কার্ড সাথে আনুন।</span>
                                </div>
                            </div>

                            <div class="right-side">
                                <button type="submit" class="theme-btn btn-style-one btn-md w-100">Confirm Booking</button>
                            </div>

                            <div class="ca-note">
                                <i class="fa fa-circle-info"></i>
                                Dates shown in red are already booked and can't be selected.
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Flatpickr -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <style>
        /* Booked dates - red background, not clickable */
        .flatpickr-day.flatpickr-disabled,
        .flatpickr-day.flatpickr-disabled:hover {
            background: #ffe0e0 !important;
            color: #dc3545 !important;
            text-decoration: line-through !important;
            cursor: not-allowed !important;
            opacity: 1 !important;
            border-radius: 4px;
        }

        /* Today highlight */
        .flatpickr-day.today {
            border-color: #0d6efd !important;
        }

        /* Selected date */
        .flatpickr-day.selected,
        .flatpickr-day.selected:hover {
            background: #0d6efd !important;
            border-color: #0d6efd !important;
        }

        /* Range between check-in and check-out */
        .flatpickr-day.inRange {
            background: #cfe2ff !important;
            border-color: #cfe2ff !important;
            box-shadow: -5px 0 0 #cfe2ff, 5px 0 0 #cfe2ff !important;
        }

        /* Past dates */
        .flatpickr-day.flatpickr-disabled[aria-label] {
            pointer-events: none;
        }
    </style>

    <script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function () {

        const now = new Date();
        const todayStr = now.toISOString().split('T')[0];
        const currentTime = now.getHours() * 60 + now.getMinutes(); // minutes since midnight

        const rawBookings = {!! json_encode($bookings) !!};

        // Hourly booking এ আজকের date block হবে কিনা check
        function isHourlyStillOccupied(booking) {
            if (!booking.is_hourly) return true;
            if (booking.start !== todayStr) return true; // আজকে না হলে block

            // checkout time parse করো
            const timeParts = booking.checkout_time.split(':');
            const checkoutMinutes = parseInt(timeParts[0]) * 60 + parseInt(timeParts[1]);
            return currentTime < checkoutMinutes; // এখনও occupied?
        }

        // Blocked dates বানাও
        const blockedDates = rawBookings.flatMap(booking => {
            if (!isHourlyStillOccupied(booking)) return []; // hourly শেষ হলে block করো না

            const start = new Date(booking.start);
            const end = new Date(booking.end);
            end.setDate(end.getDate() - 1); // checkout date open

            const dates = [];
            while (start <= end) {
                dates.push(start.toISOString().split('T')[0]);
                start.setDate(start.getDate() + 1);
            }
            return dates;
        });

        // Checkout only dates (শুধু checkout হিসেবে valid, check-in না)
        const checkoutOnlyDates = rawBookings.map(b => {
            if (!isHourlyStillOccupied(b)) return null;
            return b.end ? new Date(b.end).toISOString().split('T')[0] : null;
        }).filter(Boolean);

        // Check-in calendar এ checkout-only dates ও disable
        const blockedForCheckin = [...new Set([...blockedDates, ...checkoutOnlyDates])];

        // পরবর্তী blocked date খোঁজো (check-in এর পরে)
        function getNextBlockedDate(startDate) {
            let current = new Date(startDate);
            current.setDate(current.getDate() + 1);

            while (true) {
                const str = current.toISOString().split('T')[0];
                if (blockedDates.includes(str)) return str; // checkout এর আগের blocked
                if (current > new Date("2099-12-31")) break;
                current.setDate(current.getDate() + 1);
            }
            return null;
        }

        // Red color apply
        function applyBlockedStyle(fp) {
            setTimeout(() => {
                fp.calendarContainer?.querySelectorAll('.flatpickr-day').forEach(dayEl => {
                    if (!dayEl.dateObj) return;
                    const dateStr = dayEl.dateObj.toISOString().split('T')[0];
                    if (blockedDates.includes(dateStr)) {
                        dayEl.style.background = '#ffe0e0';
                        dayEl.style.color = '#dc3545';
                        dayEl.style.textDecoration = 'line-through';
                        dayEl.style.cursor = 'not-allowed';
                        dayEl.style.borderRadius = '4px';
                    }
                });
            }, 10);
        }

        // ===== Night / Day count display =====
        // Calculates the number of nights between the currently selected
        // check-in and check-out dates and renders it inside #nightCountBox.
        // Example: check-in 2026-07-19, check-out 2026-07-20  -> "1 Night"
        //          check-in 2026-07-19, check-out 2026-07-21  -> "2 Nights"
        const nightCountBox = document.getElementById('nightCountBox');
        const nightCountValue = document.getElementById('nightCountValue');
        const MS_PER_DAY = 1000 * 60 * 60 * 24;

        function updateNightCount() {
            const inVal = document.getElementById('arrival_date').value;
            const outVal = document.getElementById('departure_date').value;

            if (!inVal || !outVal) {
                nightCountBox.classList.add('is-hidden');
                return;
            }

            const inDate = new Date(inVal);
            const outDate = new Date(outVal);
            const diffDays = Math.round((outDate - inDate) / MS_PER_DAY);

            if (diffDays <= 0) {
                nightCountBox.classList.add('is-hidden');
                return;
            }

            nightCountValue.textContent = diffDays + ' ' + (diffDays > 1 ? 'Nights' : 'Night');
            nightCountBox.classList.remove('is-hidden');
        }

        const arrivalPicker = flatpickr("#arrival_date", {
            dateFormat: "Y-m-d",
            disable: blockedForCheckin, // checkout-only date এ check-in করা যাবে না
            minDate: "today",
            onReady(_, __, fp) { applyBlockedStyle(fp); },
            onMonthChange(_, __, fp) { applyBlockedStyle(fp); },
            onYearChange(_, __, fp) { applyBlockedStyle(fp); },
            onChange(selectedDates, _, fp) {
                applyBlockedStyle(fp);
                if (selectedDates.length > 0) {
                    const checkInDate = selectedDates[0];
                    const checkInStr = checkInDate.toISOString().split('T')[0];

                    // পরবর্তী blocked date = checkout max
                    const nextBlocked = getNextBlockedDate(checkInDate);

                    // Check-out minimum = check-in + 1 day (কমপক্ষে ১ রাত থাকতে হবে)
                    let nextDay = new Date(checkInDate);
                    nextDay.setDate(nextDay.getDate() + 1);
                    const nextDayStr = nextDay.toISOString().split('T')[0];

                    departurePicker.set("minDate", nextDayStr);
                    // nextBlocked এর পরের দিন পর্যন্ত checkout করা যাবে (ওই দিনটা checkout হিসেবে valid)
                    departurePicker.set("maxDate", nextBlocked ?? "2099-12-31");
                    departurePicker.set("disable", [...blockedDates.filter(d => d !== nextBlocked), checkInStr]);
                    departurePicker.clear();
                    departurePicker.open();
                }
                updateNightCount();
            },
        });

        const departurePicker = flatpickr("#departure_date", {
            dateFormat: "Y-m-d",
            disable: blockedDates,
            minDate: "today",
            onReady(_, __, fp) { applyBlockedStyle(fp); },
            onMonthChange(_, __, fp) { applyBlockedStyle(fp); },
            onYearChange(_, __, fp) { applyBlockedStyle(fp); },
            onChange(selectedDates, _, fp) {
                applyBlockedStyle(fp);

                // Hard safeguard: check-out can never be the same day as, or
                // earlier than, check-in — even if minDate/disable somehow
                // gets bypassed (e.g. stale picker state).
                const inVal = document.getElementById('arrival_date').value;
                if (selectedDates.length > 0 && inVal) {
                    const inDate = new Date(inVal);
                    const outDate = selectedDates[0];

                    if (outDate <= inDate) {
                        fp.clear();
                        if (window.Swal) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Invalid Check-out Date',
                                text: 'Check-out date must be at least 1 day after check-in date.',
                                confirmButtonColor: '#B68A4E'
                            });
                        } else {
                            alert('Check-out date must be at least 1 day after check-in date.');
                        }
                        nightCountBox.classList.add('is-hidden');
                        return;
                    }
                }

                updateNightCount();
            },
        });

        // Extra safety net: block form submission if, for any reason, the
        // two dates in the fields are equal or check-out is before check-in.
        document.querySelector('.check-availability form')?.addEventListener('submit', function (e) {
            const inVal = document.getElementById('arrival_date').value;
            const outVal = document.getElementById('departure_date').value;
            if (inVal && outVal && new Date(outVal) <= new Date(inVal)) {
                e.preventDefault();
                if (window.Swal) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Invalid Dates',
                        text: 'Check-out date must be at least 1 day after check-in date.',
                        confirmButtonColor: '#B68A4E'
                    });
                } else {
                    alert('Check-out date must be at least 1 day after check-in date.');
                }
            }
        });
    });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session("success") }}',
                confirmButtonColor: '#3085d6',
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false
            });
        });
    </script>
    @endif

    <!-- ============== Custom modal engine (no Bootstrap JS required) ============== -->
    <script>
    (function () {
        function openModal(id) {
            var modal = document.getElementById(id);
            if (!modal) return;
            modal.classList.add('is-open');
            modal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('room-modal-lock');
        }

        function closeModal(id) {
            var modal = document.getElementById(id);
            if (!modal) return;
            modal.classList.remove('is-open');
            modal.setAttribute('aria-hidden', 'true');
            // Only unlock scroll if no other custom modal is still open
            if (!document.querySelector('.custom-modal.is-open')) {
                document.body.classList.remove('room-modal-lock');
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[data-modal-open]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    openModal(btn.getAttribute('data-modal-open'));
                });
            });

            document.querySelectorAll('[data-modal-close]').forEach(function (el) {
                el.addEventListener('click', function () {
                    var modal = el.closest('.custom-modal');
                    if (modal) closeModal(modal.id);
                });
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    document.querySelectorAll('.custom-modal.is-open').forEach(function (m) {
                        closeModal(m.id);
                    });
                }
            });
        });

        // Expose helpers globally (used by the validation-error auto-open block below)
        window.openRoomModal = openModal;
        window.closeRoomModal = closeModal;
    })();
    </script>

    @if ($errors->any() && ($errors->has('arrival_date') || $errors->has('departure_date') || $errors->has('customer_name') || $errors->has('customer_phone') || $errors->has('customer_address')))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.openRoomModal) {
                window.openRoomModal('bookingModal');
            }
        });
    </script>
    @endif
@endsection
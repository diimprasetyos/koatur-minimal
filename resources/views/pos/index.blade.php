@extends('pos.layouts.app')

@section('title', 'Kasir')

@section('content')
    <style>
        /* ── Variables ─────────────────────────────────────────────────── */
        :root {
            --blue: #2563EB;
            --blue-dark: #1D4ED8;
            --blue-light: #EFF6FF;
            --text: #0F172A;
            --text-muted: #64748B;
            --text-faint: #94A3B8;
            --border: #E2E8F0;
            --bg: #F1F5F9;
            --white: #FFFFFF;
            --green: #10B981;
            --green-bg: #ECFDF5;
            --green-text: #065F46;
            --red: #EF4444;
            --red-bg: #FEF2F2;
            --radius: 14px;
            --radius-sm: 10px;
            --radius-xs: 7px;
            --header-h: 58px;
        }

        /* ── Layout ────────────────────────────────────────────────────── */
        .pos-root {
            display: flex;
            flex-direction: column;
            height: 100dvh;
            overflow: hidden;
            background: var(--bg);
        }

        /* ── Header ────────────────────────────────────────────────────── */
        .pos-header {
            height: var(--header-h);
            background: var(--white);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            padding: 0 16px;
            gap: 12px;
            flex-shrink: 0;
            z-index: 20;
            padding-top: env(safe-area-inset-top, 0);
        }

        .pos-header-logo {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: var(--blue);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .pos-header-logo svg {
            width: 17px;
            height: 17px;
            color: #fff;
        }

        .pos-header-store {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .pos-header-store-name {
            font-size: 14px;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .pos-header-store-sub {
            font-size: 11px;
            color: var(--text-muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .pos-header-right {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }

        /* Clock — desktop only */
        .pos-clock {
            display: none;
            flex-direction: column;
            align-items: flex-end;
        }

        @media (min-width: 768px) {
            .pos-clock {
                display: flex;
            }
        }

        .pos-clock-time {
            font-size: 13px;
            font-weight: 600;
            font-family: 'DM Mono', monospace;
            color: var(--text);
            letter-spacing: .5px;
        }

        .pos-clock-date {
            font-size: 11px;
            color: var(--text-muted);
        }

        .avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--blue-light);
            color: var(--blue);
            font-size: 12px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .pos-cashier-name {
            font-size: 13px;
            font-weight: 500;
            color: var(--text);
            display: none;
        }

        @media (min-width: 640px) {
            .pos-cashier-name {
                display: block;
            }
        }

        .btn-logout {
            background: none;
            border: 1px solid var(--border);
            font-family: inherit;
            font-size: 12px;
            font-weight: 500;
            color: var(--text-muted);
            cursor: pointer;
            padding: 5px 10px;
            border-radius: 8px;
            transition: all .15s;
            white-space: nowrap;
            -webkit-tap-highlight-color: transparent;
        }

        .btn-logout:hover {
            border-color: var(--red);
            color: var(--red);
        }

        /* ── Body ──────────────────────────────────────────────────────── */
        .pos-body {
            flex: 1;
            display: flex;
            overflow: hidden;
        }

        /* ── Product Panel ─────────────────────────────────────────────── */
        .product-panel {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            border-right: 1px solid var(--border);
        }

        /* Search + Category */
        .search-bar {
            background: var(--white);
            border-bottom: 1px solid var(--border);
            padding: 10px 12px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            flex-shrink: 0;
        }

        .search-input-wrap {
            position: relative;
        }

        .search-icon {
            position: absolute;
            left: 11px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-faint);
            pointer-events: none;
        }

        .search-icon svg {
            width: 16px;
            height: 16px;
        }

        .search-input {
            width: 100%;
            height: 40px;
            padding: 0 12px 0 36px;
            font-family: inherit;
            font-size: 14px;
            color: var(--text);
            background: var(--bg);
            border: 1.5px solid var(--border);
            border-radius: var(--radius-xs);
            outline: none;
            transition: border-color .15s, background .15s;
            -webkit-appearance: none;
        }

        .search-input:focus {
            border-color: var(--blue);
            background: var(--white);
        }

        .search-input::placeholder {
            color: var(--text-faint);
        }

        .category-scroll {
            display: flex;
            gap: 6px;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
            padding-bottom: 2px;
        }

        .category-scroll::-webkit-scrollbar {
            display: none;
        }

        .cat-btn {
            display: inline-flex;
            align-items: center;
            padding: 5px 14px;
            border-radius: 99px;
            font-family: inherit;
            font-size: 12px;
            font-weight: 600;
            border: 1.5px solid var(--border);
            background: var(--white);
            color: var(--text-muted);
            cursor: pointer;
            white-space: nowrap;
            flex-shrink: 0;
            transition: all .15s;
            -webkit-tap-highlight-color: transparent;
        }

        .cat-btn:hover {
            border-color: var(--blue-mid);
            color: var(--blue);
        }

        .cat-btn.active {
            background: var(--blue);
            border-color: var(--blue);
            color: #fff;
        }

        /* Products Grid */
        .products-scroll {
            flex: 1;
            overflow-y: auto;
            padding: 12px;
            padding-bottom: calc(80px + env(safe-area-inset-bottom, 0px));
        }

        @media (min-width: 768px) {
            .products-scroll {
                padding-bottom: 12px;
            }
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }

        @media (min-width: 480px) {
            .products-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (min-width: 768px) {
            .products-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (min-width: 1024px) {
            .products-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        @media (min-width: 1280px) {
            .products-grid {
                grid-template-columns: repeat(5, 1fr);
            }
        }

        /* Product Card */
        .product-card {
            background: var(--white);
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            overflow: hidden;
            cursor: pointer;
            transition: border-color .15s, transform .1s, box-shadow .15s;
            position: relative;
            -webkit-tap-highlight-color: transparent;
            user-select: none;
        }

        .product-card:hover {
            border-color: var(--blue-mid);
            box-shadow: 0 2px 10px rgba(37, 99, 235, .08);
        }

        .product-card:active {
            transform: scale(.97);
        }

        .product-card.out-of-stock {
            opacity: .5;
            cursor: not-allowed;
        }

        .product-card.in-cart {
            border-color: var(--blue);
        }

        .product-img {
            width: 100%;
            aspect-ratio: 1/1;
            background: var(--bg);
            overflow: hidden;
            position: relative;
        }

        .product-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-img-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-faint);
        }

        .product-img-placeholder svg {
            width: 32px;
            height: 32px;
            opacity: .4;
        }

        .product-badge {
            position: absolute;
            top: 6px;
            right: 6px;
            background: var(--blue);
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            font-family: 'DM Mono', monospace;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 2;
        }

        .product-card.in-cart .product-badge {
            display: flex;
        }

        .product-info {
            padding: 10px 10px 12px;
        }

        .product-name {
            font-size: 13px;
            font-weight: 600;
            color: var(--text);
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            line-height: 1.3;
            margin-bottom: 4px;
        }

        .product-price {
            font-size: 13px;
            font-weight: 700;
            color: var(--blue);
            font-family: 'DM Mono', monospace;
        }

        .product-stock {
            font-size: 11px;
            color: var(--text-faint);
            margin-top: 2px;
        }

        .product-stock.low {
            color: var(--red);
        }

        /* Empty & Loading */
        .products-empty,
        .products-loading {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px 20px;
            grid-column: 1 / -1;
            color: var(--text-faint);
            gap: 8px;
        }

        .products-empty svg {
            opacity: .3;
        }

        .products-empty p {
            font-size: 14px;
            color: var(--text-muted);
        }

        .spinner {
            width: 28px;
            height: 28px;
            border: 2.5px solid var(--border);
            border-top-color: var(--blue);
            border-radius: 50%;
            animation: spin .7s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* ── Cart Sidebar (Desktop md+) ───────────────────────────────── */
        .cart-sidebar {
            display: none;
            flex-direction: column;
            width: 280px;
            flex-shrink: 0;
            background: var(--white);
            overflow: hidden;
        }

        @media (min-width: 768px) {
            .cart-sidebar {
                display: flex;
                width: 280px;
            }
        }

        @media (min-width: 1024px) {
            .cart-sidebar {
                width: 320px;
            }
        }

        @media (min-width: 1280px) {
            .cart-sidebar {
                width: 360px;
            }
        }

        /* ── FAB Cart Button (Mobile) ─────────────────────────────────── */
        .fab-cart {
            display: none;
            /* shown by JS when cart has items on mobile */
            position: fixed;
            bottom: calc(20px + env(safe-area-inset-bottom, 0px));
            right: 16px;
            z-index: 40;
            background: var(--blue);
            color: #fff;
            border: none;
            border-radius: 16px;
            padding: 0 18px;
            height: 52px;
            align-items: center;
            gap: 10px;
            font-family: inherit;
            font-size: 14px;
            font-weight: 600;
            box-shadow: 0 4px 20px rgba(37, 99, 235, .35);
            cursor: pointer;
            transition: transform .15s, box-shadow .15s;
            -webkit-tap-highlight-color: transparent;
        }

        .fab-cart:active {
            transform: scale(.96);
        }

        .fab-badge {
            background: rgba(255, 255, 255, .25);
            font-size: 11px;
            font-family: 'DM Mono', monospace;
            padding: 1px 7px;
            border-radius: 99px;
        }

        .fab-total {
            font-family: 'DM Mono', monospace;
            font-size: 13px;
        }

        /* ── Mobile Bottom Sheet ─────────────────────────────────────── */
        .sheet-overlay {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 50;
            background: rgba(0, 0, 0, .4);
            backdrop-filter: blur(2px);
            -webkit-backdrop-filter: blur(2px);
        }

        .sheet-overlay.open {
            display: block;
        }

        .sheet {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 51;
            background: var(--white);
            border-radius: 20px 20px 0 0;
            max-height: 92dvh;
            display: flex;
            flex-direction: column;
            transform: translateY(100%);
            transition: transform .28s cubic-bezier(.32, 1, .36, 1);
            padding-bottom: env(safe-area-inset-bottom, 0px);
        }

        .sheet.open {
            transform: translateY(0);
        }

        .sheet-handle-bar {
            flex-shrink: 0;
            display: flex;
            justify-content: center;
            padding: 12px 0 6px;
            cursor: pointer;
        }

        .sheet-handle {
            width: 36px;
            height: 4px;
            background: var(--border);
            border-radius: 99px;
        }

        .sheet-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 16px 12px;
            flex-shrink: 0;
        }

        .sheet-title {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 16px;
            font-weight: 600;
            color: var(--text);
        }

        .sheet-badge {
            background: var(--blue-light);
            color: var(--blue);
            font-size: 11px;
            font-family: 'DM Mono', monospace;
            padding: 2px 8px;
            border-radius: 99px;
        }

        .sheet-close {
            background: var(--bg);
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--text-muted);
            -webkit-tap-highlight-color: transparent;
        }

        .sheet-close svg {
            width: 16px;
            height: 16px;
        }

        /* Cart item rows (shared desktop + mobile) */
        .cart-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            background: var(--white);
            border-radius: var(--radius-xs);
            border: 1px solid var(--border);
            animation: fadeUp .15s ease both;
        }

        .cart-item-name {
            flex: 1;
            min-width: 0;
            font-size: 13px;
            font-weight: 500;
            color: var(--text);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .cart-item-price {
            font-size: 12px;
            color: var(--text-muted);
            font-family: 'DM Mono', monospace;
            margin-top: 2px;
        }

        .qty-ctrl {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-shrink: 0;
        }

        .qty-btn {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            border: 1.5px solid var(--border);
            background: var(--bg);
            color: var(--text);
            font-size: 16px;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all .12s;
            -webkit-tap-highlight-color: transparent;
            flex-shrink: 0;
        }

        .qty-btn:hover {
            border-color: var(--blue);
            color: var(--blue);
        }

        .qty-btn.minus:hover {
            border-color: var(--red);
            color: var(--red);
        }

        .qty-val {
            font-family: 'DM Mono', monospace;
            font-size: 14px;
            font-weight: 600;
            min-width: 20px;
            text-align: center;
            color: var(--text);
        }

        /* Sheet body (scrollable items + fixed footer) */
        .sheet-body {
            flex: 1;
            overflow-y: auto;
            padding: 0 16px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            -webkit-overflow-scrolling: touch;
        }

        .sheet-footer {
            flex-shrink: 0;
            padding: 16px;
            border-top: 1px solid var(--border);
        }

        /* Summary (shared) */
        .s-summary {
            display: flex;
            flex-direction: column;
            gap: 5px;
            margin-bottom: 14px;
        }

        .s-row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            color: var(--text-muted);
        }

        .s-row.total {
            border-top: 1px dashed var(--border);
            padding-top: 8px;
            margin-top: 2px;
            font-size: 16px;
            font-weight: 700;
            color: var(--text);
        }

        .s-total-val {
            color: var(--blue);
            font-family: 'DM Mono', monospace;
        }

        /* Payment section (shared) */
        .p-section {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .cash-row-m {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .cash-label-m {
            font-size: 12px;
            font-weight: 500;
            color: var(--text-muted);
        }

        .cash-wrap {
            position: relative;
        }

        .cash-pre {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 13px;
            color: var(--text-muted);
            font-family: 'DM Mono', monospace;
            pointer-events: none;
        }

        .cash-inp {
            width: 100%;
            height: 44px;
            padding: 0 12px 0 34px;
            font-family: 'DM Mono', monospace;
            font-size: 15px;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-xs);
            outline: none;
            color: var(--text);
            background: var(--bg);
            transition: border-color .15s;
            -webkit-appearance: none;
        }

        .cash-inp:focus {
            border-color: var(--blue);
            background: var(--white);
        }

        .change-row-m {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--green-bg);
            border-radius: var(--radius-xs);
            padding: 10px 14px;
            font-size: 14px;
            font-weight: 600;
            color: var(--green-text);
        }

        .change-mono {
            font-family: 'DM Mono', monospace;
        }

        .method-g {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 7px;
        }

        .m-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 3px;
            padding: 9px 4px;
            font-family: inherit;
            font-size: 11px;
            font-weight: 600;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-xs);
            background: var(--white);
            color: var(--text-muted);
            cursor: pointer;
            transition: all .15s;
            -webkit-tap-highlight-color: transparent;
        }

        .m-btn:hover {
            border-color: var(--blue-mid);
            color: var(--blue);
        }

        .m-btn.active {
            border-color: var(--blue);
            background: var(--blue-light);
            color: var(--blue);
        }

        .m-icon {
            font-size: 18px;
        }

        .btn-pay-m {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 50px;
            background: var(--blue);
            color: #fff;
            font-family: inherit;
            font-size: 15px;
            font-weight: 700;
            border: none;
            border-radius: var(--radius-xs);
            cursor: pointer;
            transition: background .15s, transform .1s;
            -webkit-tap-highlight-color: transparent;
        }

        .btn-pay-m:hover {
            background: var(--blue-dark);
        }

        .btn-pay-m:active {
            transform: scale(.98);
        }

        .btn-pay-m:disabled {
            opacity: .5;
            cursor: not-allowed;
            transform: none;
        }

        .btn-cancel-m {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 40px;
            margin-top: 6px;
            background: none;
            color: var(--text-muted);
            font-family: inherit;
            font-size: 13px;
            font-weight: 500;
            border: 1px solid var(--border);
            border-radius: var(--radius-xs);
            cursor: pointer;
            transition: all .15s;
            -webkit-tap-highlight-color: transparent;
        }

        .btn-cancel-m:hover {
            border-color: var(--red);
            color: var(--red);
        }

        /* ── Receipt Modal ───────────────────────────────────────────── */
        .modal-bg {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 60;
            background: rgba(0, 0, 0, .5);
            backdrop-filter: blur(3px);
            -webkit-backdrop-filter: blur(3px);
            align-items: flex-end;
            justify-content: center;
            padding: 0;
        }

        @media (min-width: 600px) {
            .modal-bg {
                align-items: center;
                padding: 24px;
            }
        }

        .modal-bg.open {
            display: flex;
        }

        .modal {
            background: var(--white);
            border-radius: 20px 20px 0 0;
            width: 100%;
            max-width: 480px;
            padding: 0 0 calc(20px + env(safe-area-inset-bottom, 0px));
            animation: sheetIn .25s cubic-bezier(.32, 1, .36, 1) both;
            max-height: 90dvh;
            overflow-y: auto;
        }

        @media (min-width: 600px) {
            .modal {
                border-radius: 20px;
                padding-bottom: 24px;
                animation: fadeUp .2s ease both;
            }
        }

        .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 20px 0;
        }

        .modal-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--text);
        }

        .modal-close {
            background: var(--bg);
            border: none;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--text-muted);
            -webkit-tap-highlight-color: transparent;
        }

        .modal-close svg {
            width: 16px;
            height: 16px;
        }

        .receipt-content {
            padding: 16px 20px;
        }

        .receipt-store {
            text-align: center;
            margin-bottom: 16px;
        }

        .receipt-store-name {
            font-size: 16px;
            font-weight: 700;
            color: var(--text);
        }

        .receipt-meta {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .receipt-items {
            border-top: 1px dashed var(--border);
            border-bottom: 1px dashed var(--border);
            padding: 12px 0;
            margin: 12px 0;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .receipt-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
            color: var(--text);
        }

        .receipt-item-name {
            flex: 1;
            padding-right: 8px;
        }

        .receipt-item-amt {
            font-family: 'DM Mono', monospace;
            white-space: nowrap;
        }

        .receipt-totals {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .receipt-row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            color: var(--text-muted);
        }

        .receipt-row.grand {
            font-size: 16px;
            font-weight: 700;
            color: var(--text);
            border-top: 1px solid var(--border);
            padding-top: 8px;
            margin-top: 4px;
        }

        .receipt-row.change {
            color: var(--green);
            font-weight: 600;
        }

        .receipt-mono {
            font-family: 'DM Mono', monospace;
        }

        .receipt-thanks {
            text-align: center;
            font-size: 12px;
            color: var(--text-faint);
            margin-top: 16px;
            padding-top: 12px;
            border-top: 1px dashed var(--border);
        }

        .modal-actions {
            display: flex;
            gap: 8px;
            padding: 0 20px;
        }

        .btn-print {
            flex: 1;
            height: 46px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-xs);
            background: none;
            color: var(--text-muted);
            font-family: inherit;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all .15s;
            -webkit-tap-highlight-color: transparent;
        }

        .btn-print:hover {
            border-color: var(--text);
            color: var(--text);
        }

        .btn-print svg {
            width: 16px;
            height: 16px;
        }

        .btn-new {
            flex: 2;
            height: 46px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--blue);
            color: #fff;
            border: none;
            border-radius: var(--radius-xs);
            font-family: inherit;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: background .15s;
            -webkit-tap-highlight-color: transparent;
        }

        .btn-new:hover {
            background: var(--blue-dark);
        }

        @keyframes sheetIn {
            from {
                transform: translateY(60px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(6px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media print {
            body>*:not(#receipt-modal) {
                display: none !important;
            }

            .modal-bg {
                display: block !important;
                background: none;
                position: static;
            }

            .modal {
                box-shadow: none;
                border-radius: 0;
                max-height: none;
            }

            .modal-header,
            .modal-actions {
                display: none;
            }
        }
    </style>

    <div class="pos-root">

        {{-- ═══ HEADER ═════════════════════════════════════════════════════════ --}}
        <header class="pos-header">
            <div class="pos-header-logo">
                <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <div class="pos-header-store">
                <div class="pos-header-store-name">{{ $tenant->name ?? config('app.name') }}</div>
                @if (isset($tenant->slug))
                    <div class="pos-header-store-sub">{{ $tenant->slug }}</div>
                @endif
            </div>
            <div class="pos-header-right">
                <div class="pos-clock">
                    <div class="pos-clock-time" id="pos-time">00:00:00</div>
                    <div class="pos-clock-date" id="pos-date"></div>
                </div>
                <div class="avatar">{{ strtoupper(substr(auth('pos')->user()->name, 0, 1)) }}</div>
                <span class="pos-cashier-name">{{ auth('pos')->user()->name }}</span>
                <form method="POST" action="{{ route('pos.logout') }}" style="margin:0">
                    @csrf
                    <button type="submit" class="btn-logout">Keluar</button>
                </form>
            </div>
        </header>

        {{-- ═══ BODY ════════════════════════════════════════════════════════════ --}}
        <div class="pos-body">

            {{-- ─── Products ─────────────────────────────────────────────────── --}}
            <div class="product-panel">
                <div class="search-bar">
                    <div class="search-input-wrap">
                        <span class="search-icon">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input type="text" id="search-input" placeholder="Cari produk… (F2)" class="search-input">
                    </div>
                    <div class="category-scroll">
                        <button class="cat-btn active" data-category="">Semua</button>
                        @foreach ($categories as $cat)
                            <button class="cat-btn" data-category="{{ $cat->id }}"
                                style="{{ $cat->color ? 'border-color:' . $cat->color : '' }}">
                                {{ $cat->name }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="products-scroll">
                    <div id="products-grid" class="products-grid">
                        <div class="products-loading">
                            <div class="spinner"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ─── Cart Sidebar (Desktop md+) ──────────────────────────────── --}}
            <aside class="cart-sidebar" id="cart-sidebar">
                @include('pos.partials._cart-panel')
            </aside>

        </div>{{-- end body --}}
    </div>{{-- end root --}}

    {{-- ═══ FAB (Mobile) ══════════════════════════════════════════════════════ --}}
    <button id="fab-cart" class="fab-cart" onclick="openSheet()" aria-label="Buka keranjang">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
        </svg>
        <span id="fab-badge" class="fab-badge">0</span>
        <span id="fab-total" class="fab-total">Rp 0</span>
    </button>

    {{-- ═══ Sheet Overlay ═══════════════════════════════════════════════════════ --}}
    <div id="sheet-overlay" class="sheet-overlay" onclick="closeSheet()"></div>

    {{-- ═══ Bottom Sheet (Mobile) ════════════════════════════════════════════ --}}
    <div id="cart-sheet" class="sheet">
        <div class="sheet-handle-bar" onclick="closeSheet()">
            <div class="sheet-handle"></div>
        </div>
        <div class="sheet-header">
            <div class="sheet-title">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                Keranjang
                <span id="sheet-badge" class="sheet-badge">0</span>
            </div>
            <button class="sheet-close" onclick="closeSheet()" aria-label="Tutup">
                <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div id="sheet-items" class="sheet-body">
            {{-- filled by JS --}}
        </div>

        <div class="sheet-footer">
            <div class="s-summary">
                <div class="s-row">
                    <span>Subtotal</span>
                    <span id="sheet-subtotal" class="receipt-mono">Rp 0</span>
                </div>
                <div class="s-row">
                    <span>Diskon</span>
                    <span id="sheet-discount" class="receipt-mono" style="color:var(--red)">- Rp 0</span>
                </div>
                <div class="s-row total">
                    <span>Total</span>
                    <span id="sheet-total" class="s-total-val">Rp 0</span>
                </div>
            </div>

            <div id="sheet-pay-section" class="p-section hidden">
                <div class="cash-row-m">
                    <label class="cash-label-m">Uang Diterima</label>
                    <div class="cash-wrap">
                        <span class="cash-pre">Rp</span>
                        <input type="number" id="paid-input-mobile" placeholder="0" class="cash-inp"
                            oninput="updateChange('mobile')">
                    </div>
                </div>
                <div class="change-row-m">
                    <span>Kembalian</span>
                    <span id="change-display-mobile" class="change-mono">Rp 0</span>
                </div>
                <div class="method-g">
                    <button onclick="setPayment('cash')" data-method="cash" class="m-btn active">
                        <span class="m-icon">💵</span> Tunai
                    </button>
                    <button onclick="setPayment('transfer')" data-method="transfer" class="m-btn">
                        <span class="m-icon">🏦</span> Transfer
                    </button>
                    <button onclick="setPayment('ewallet')" data-method="ewallet" class="m-btn">
                        <span class="m-icon">📱</span> E-Wallet
                    </button>
                </div>
                <button id="pay-btn-mobile" onclick="processPayment()" class="btn-pay-m">
                    Bayar Sekarang
                </button>
                <button onclick="clearCart()" class="btn-cancel-m">Batalkan Transaksi</button>
            </div>

            <div id="sheet-empty-cta"
                style="text-align:center; padding: 8px 0; font-size: 13px; color: var(--text-faint);">
                Belum ada produk dipilih
            </div>
        </div>
    </div>

    {{-- ═══ Receipt Modal ════════════════════════════════════════════════════ --}}
    <div id="receipt-modal" class="modal-bg">
        <div class="modal">
            <div class="modal-header">
                <div class="modal-title">Struk Pembayaran</div>
                <button class="modal-close" onclick="closeReceipt()" aria-label="Tutup">
                    <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div id="receipt-content" class="receipt-content"></div>
            <div class="modal-actions">
                <button class="btn-print" onclick="openPrintModal()">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Cetak
                </button>
                <button class="btn-new" onclick="closeReceipt()">Transaksi Baru</button>
            </div>
        </div>
    </div>

    @include('pos.partials._print-modal')

    @push('scripts')
        <script>
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

            const state = {
                cart: [],
                paymentMethod: 'cash',
                activeCategory: '',
                searchQuery: '',
                currentSaleId: null,
            };

            // ─── Mobile detection ─────────────────────────────────────────────────────
            const isMobile = () => window.innerWidth < 768;

            // ─── Sheet ────────────────────────────────────────────────────────────────
            function openSheet() {
                document.getElementById('sheet-overlay').classList.add('open');
                document.getElementById('cart-sheet').classList.add('open');
                document.body.style.overflow = 'hidden';
            }

            function closeSheet() {
                document.getElementById('sheet-overlay').classList.remove('open');
                document.getElementById('cart-sheet').classList.remove('open');
                document.body.style.overflow = '';
            }

            // ─── Clock ────────────────────────────────────────────────────────────────
            function tickClock() {
                const now = new Date();
                const t = now.toLocaleTimeString('id-ID', {
                    hour12: false
                });
                const d = now.toLocaleDateString('id-ID', {
                    weekday: 'short',
                    day: 'numeric',
                    month: 'short'
                });
                const tel = document.getElementById('pos-time');
                const del = document.getElementById('pos-date');
                if (tel) tel.textContent = t;
                if (del) del.textContent = d;
            }
            tickClock();
            setInterval(tickClock, 1000);

            // ─── Load Products ────────────────────────────────────────────────────────
            async function loadProducts() {
                const grid = document.getElementById('products-grid');
                grid.innerHTML = '<div class="products-loading"><div class="spinner"></div></div>';
                try {
                    const params = new URLSearchParams();
                    if (state.activeCategory) params.set('category', state.activeCategory);
                    if (state.searchQuery) params.set('search', state.searchQuery);

                    const res = await fetch(`{{ route('pos.api.products') }}?${params}`, {
                        headers: {
                            Accept: 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                    });
                    const products = await res.json();

                    if (!products.length) {
                        grid.innerHTML = `<div class="products-empty">
                <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                </svg>
                <p>Produk tidak ditemukan</p></div>`;
                        return;
                    }
                    grid.innerHTML = products.map(p => renderProductCard(p)).join('');
                    updateCartBadgesOnGrid();
                } catch {
                    grid.innerHTML = `<div class="products-empty"><p>Gagal memuat produk.</p></div>`;
                }
            }

            function renderProductCard(p) {
                const cartItem = state.cart.find(c => c.product.id === p.id);
                const qty = cartItem ? cartItem.qty : 0;
                const inCart = qty > 0;
                const oos = !p.in_stock;
                return `
    <div class="product-card ${inCart ? 'in-cart' : ''} ${oos ? 'out-of-stock' : ''}"
         id="pc-${p.id}"
         onclick="${oos ? '' : `addToCart(${JSON.stringify(p).replace(/"/g, '&quot;')})`}"
         role="button" tabindex="0"
         aria-label="${p.name}">
        <div class="product-img">
            ${p.image
                ? `<img src="${p.image}" alt="${p.name}" loading="lazy">`
                : `<div class="product-img-placeholder">
                            <svg fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                            </svg>
                           </div>`
            }
            <div class="product-badge">${qty || ''}</div>
        </div>
        <div class="product-info">
            <div class="product-name">${p.name}</div>
            <div class="product-price">${formatRp(p.price)}</div>
            ${p.stock !== null
                ? `<div class="product-stock ${p.stock < 5 ? 'low' : ''}">Stok: ${p.stock}</div>`
                : ''}
        </div>
    </div>`;
            }

            function updateCartBadgesOnGrid() {
                document.querySelectorAll('[id^="pc-"]').forEach(el => {
                    const id = parseInt(el.id.replace('pc-', ''));
                    const item = state.cart.find(c => c.product.id === id);
                    const qty = item ? item.qty : 0;
                    el.classList.toggle('in-cart', qty > 0);
                    const badge = el.querySelector('.product-badge');
                    if (badge) {
                        badge.textContent = qty || '';
                    }
                });
            }

            // ─── Cart Logic ───────────────────────────────────────────────────────────
            function addToCart(product) {
                const idx = state.cart.findIndex(c => c.product.id === product.id);
                if (idx >= 0) {
                    state.cart[idx].qty++;
                } else {
                    state.cart.push({
                        product,
                        qty: 1
                    });
                }
                renderCart();
            }

            function changeQty(productId, delta) {
                const idx = state.cart.findIndex(c => c.product.id === productId);
                if (idx < 0) return;
                state.cart[idx].qty += delta;
                if (state.cart[idx].qty <= 0) state.cart.splice(idx, 1);
                renderCart();
            }

            function clearCart() {
                state.cart = [];
                renderCart();
                closeSheet();
            }

            function renderCart() {
                const total = state.cart.reduce((s, c) => s + c.product.price * c.qty, 0);
                const count = state.cart.reduce((s, c) => s + c.qty, 0);
                const hasItems = state.cart.length > 0;

                // ── Desktop sidebar ──
                const desktopBody = document.querySelector('#cart-sidebar .cart-body');
                const desktopEmpty = document.querySelector('#cart-sidebar #cart-empty');
                const desktopBadge = document.querySelector('#cart-sidebar .cart-badge');
                const desktopClear = document.querySelector('#cart-sidebar .cart-clear');
                const paySection = document.getElementById('payment-section');
                const emptyCta = document.getElementById('empty-cta');

                if (desktopBody) {
                    // remove old items
                    desktopBody.querySelectorAll('.cart-item').forEach(el => el.remove());
                    if (desktopEmpty) desktopEmpty.style.display = hasItems ? 'none' : '';
                    if (desktopBadge) desktopBadge.textContent = count;
                    if (desktopClear) desktopClear.classList.toggle('hidden', !hasItems);
                    state.cart.forEach(c => {
                        const el = document.createElement('div');
                        el.className = 'cart-item';
                        el.innerHTML = `
                <div style="flex:1;min-width:0">
                    <div class="cart-item-name">${c.product.name}</div>
                    <div class="cart-item-price">${formatRp(c.product.price)}</div>
                </div>
                <div class="qty-ctrl">
                    <button class="qty-btn minus" onclick="changeQty(${c.product.id},-1)">−</button>
                    <span class="qty-val">${c.qty}</span>
                    <button class="qty-btn" onclick="changeQty(${c.product.id},1)">+</button>
                </div>`;
                        desktopBody.insertBefore(el, desktopEmpty);
                    });
                    // summary
                    const sub = document.getElementById('summary-subtotal');
                    const tot = document.getElementById('summary-total');
                    if (sub) sub.textContent = formatRp(total);
                    if (tot) tot.textContent = formatRp(total);
                    if (paySection) paySection.classList.toggle('hidden', !hasItems);
                    if (emptyCta) emptyCta.classList.toggle('hidden', hasItems);
                }

                // ── Sheet (mobile) ──
                const sheetBody = document.getElementById('sheet-items');
                const sheetBadge = document.getElementById('sheet-badge');
                const sheetSub = document.getElementById('sheet-subtotal');
                const sheetTot = document.getElementById('sheet-total');
                const sheetPay = document.getElementById('sheet-pay-section');
                const sheetCta = document.getElementById('sheet-empty-cta');

                if (sheetBody) {
                    sheetBody.innerHTML = '';
                    state.cart.forEach(c => {
                        const el = document.createElement('div');
                        el.className = 'cart-item';
                        el.innerHTML = `
                <div style="flex:1;min-width:0">
                    <div class="cart-item-name">${c.product.name}</div>
                    <div class="cart-item-price">${formatRp(c.product.price)}</div>
                </div>
                <div class="qty-ctrl">
                    <button class="qty-btn minus" onclick="changeQty(${c.product.id},-1)">−</button>
                    <span class="qty-val">${c.qty}</span>
                    <button class="qty-btn" onclick="changeQty(${c.product.id},1)">+</button>
                </div>`;
                        sheetBody.appendChild(el);
                    });
                }
                if (sheetBadge) sheetBadge.textContent = count;
                if (sheetSub) sheetSub.textContent = formatRp(total);
                if (sheetTot) sheetTot.textContent = formatRp(total);
                if (sheetPay) sheetPay.classList.toggle('hidden', !hasItems);
                if (sheetCta) sheetCta.style.display = hasItems ? 'none' : '';

                // ── FAB ──
                const fab = document.getElementById('fab-cart');
                const fabBadge = document.getElementById('fab-badge');
                const fabTotal = document.getElementById('fab-total');
                if (fab) {
                    fab.style.display = (isMobile() && hasItems) ? 'flex' : 'none';
                }
                if (fabBadge) fabBadge.textContent = count;
                if (fabTotal) fabTotal.textContent = formatRp(total);

                updateCartBadgesOnGrid();
            }

            // ─── Payment method ───────────────────────────────────────────────────────
            function setPayment(method) {
                state.paymentMethod = method;
                document.querySelectorAll('[data-method]').forEach(btn => {
                    btn.classList.toggle('active', btn.dataset.method === method);
                    btn.classList.toggle('m-btn', true);
                    btn.classList.toggle('method-btn', true);
                });
                const paidD = document.getElementById('paid-input');
                const paidM = document.getElementById('paid-input-mobile');
                if (method !== 'cash') {
                    if (paidD) paidD.disabled = true;
                    if (paidM) paidM.disabled = true;
                } else {
                    if (paidD) paidD.disabled = false;
                    if (paidM) paidM.disabled = false;
                }
            }

            function updateChange(source) {
                const total = state.cart.reduce((s, c) => s + c.product.price * c.qty, 0);
                const input = source === 'mobile' ?
                    document.getElementById('paid-input-mobile') :
                    document.getElementById('paid-input');
                const dispEl = source === 'mobile' ?
                    document.getElementById('change-display-mobile') :
                    document.getElementById('change-display');
                if (!input || !dispEl) return;
                const paid = parseFloat(input.value) || 0;
                const change = paid - total;
                dispEl.textContent = formatRp(Math.max(0, change));
                dispEl.style.color = change < 0 ? 'var(--red)' : '';
            }

            // ─── Process Payment ──────────────────────────────────────────────────────
            async function processPayment() {
                if (!state.cart.length) return;
                const total = state.cart.reduce((s, c) => s + c.product.price * c.qty, 0);
                const paidD = parseFloat(document.getElementById('paid-input')?.value) || 0;
                const paidM = parseFloat(document.getElementById('paid-input-mobile')?.value) || 0;
                const paid = isMobile() ? paidM : paidD;

                if (state.paymentMethod === 'cash' && paid < total) {
                    alert('Uang diterima kurang dari total!');
                    return;
                }

                const payBtns = ['pay-btn', 'pay-btn-mobile'].map(id => document.getElementById(id)).filter(Boolean);
                payBtns.forEach(b => {
                    b.disabled = true;
                    b.textContent = 'Memproses…';
                });

                try {
                    const cr = await fetch('{{ route('pos.api.sale.create') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            Accept: 'application/json'
                        },
                        body: JSON.stringify({
                            items: state.cart.map(c => ({
                                product_id: c.product.id,
                                qty: c.qty
                            }))
                        }),
                    });
                    const cData = await cr.json();
                    if (!cr.ok) throw new Error(cData.message || 'Gagal membuat transaksi.');

                    const pr = await fetch(`/pos/api/sale/${cData.sale_id}/pay`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            Accept: 'application/json'
                        },
                        body: JSON.stringify({
                            payment_method: state.paymentMethod,
                            paid: state.paymentMethod === 'cash' ? paid : total
                        }),
                    });
                    const pData = await pr.json();
                    if (!pr.ok) throw new Error(pData.message || 'Gagal memproses pembayaran.');

                    state.currentSaleId = cData.sale_id;
                    closeSheet();
                    showReceipt(pData);

                } catch (err) {
                    alert('Error: ' + err.message);
                } finally {
                    payBtns.forEach(b => {
                        b.disabled = false;
                        b.textContent = b.id === 'pay-btn-mobile' ? 'Bayar Sekarang' : 'Bayar';
                    });
                }
            }

            // ─── Receipt ──────────────────────────────────────────────────────────────
            function showReceipt(payData) {
                const subtotal = state.cart.reduce((s, c) => s + c.product.price * c.qty, 0);
                const itemsHtml = state.cart.map(c => `
        <div class="receipt-item">
            <div class="receipt-item-name">${c.product.name} ×${c.qty}</div>
            <div class="receipt-item-amt">${formatRp(c.product.price * c.qty)}</div>
        </div>`).join('');

                document.getElementById('receipt-content').innerHTML = `
        <div class="receipt-store">
            <div class="receipt-store-name">{{ config('app.name') }}</div>
            <div class="receipt-meta">${new Date().toLocaleString('id-ID')}</div>
            <div class="receipt-meta">No: ${payData.invoice_number}</div>
        </div>
        <div class="receipt-items">${itemsHtml}</div>
        <div class="receipt-totals">
            <div class="receipt-row"><span>Subtotal</span><span class="receipt-mono">${formatRp(subtotal)}</span></div>
            <div class="receipt-row grand"><span>Total</span><span class="receipt-mono">${formatRp(payData.total)}</span></div>
            <div class="receipt-row"><span>Dibayar</span><span class="receipt-mono">${formatRp(payData.paid)}</span></div>
            <div class="receipt-row change"><span>Kembalian</span><span class="receipt-mono">${formatRp(payData.change)}</span></div>
        </div>
        <div class="receipt-thanks">Terima kasih telah berbelanja! 🙏</div>`;

                document.getElementById('receipt-modal').classList.add('open');
                window._lastPayData = payData;
                window._lastCartSnap = state.cart.map(c => ({
                    ...c
                }));
            }

            function closeReceipt() {
                document.getElementById('receipt-modal').classList.remove('open');
                const pi = document.getElementById('paid-input');
                const pm = document.getElementById('paid-input-mobile');
                if (pi) pi.value = '';
                if (pm) pm.value = '';
                clearCart();
            }

            // ─── Category filter ──────────────────────────────────────────────────────
            document.querySelectorAll('.cat-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    document.querySelectorAll('.cat-btn').forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    state.activeCategory = btn.dataset.category;
                    loadProducts();
                });
            });

            // ─── Search ───────────────────────────────────────────────────────────────
            let searchTimer;
            document.getElementById('search-input').addEventListener('input', e => {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(() => {
                    state.searchQuery = e.target.value;
                    loadProducts();
                }, 300);
            });

            // ─── Keyboard shortcuts ───────────────────────────────────────────────────
            document.addEventListener('keydown', e => {
                if (e.key === 'F2') {
                    e.preventDefault();
                    document.getElementById('search-input').focus();
                }
                if (e.key === 'F5') {
                    e.preventDefault();
                    processPayment();
                }
                if (e.key === 'Escape') {
                    closeReceipt();
                    closeSheet();
                }
            });

            // ─── Resize ───────────────────────────────────────────────────────────────
            window.addEventListener('resize', () => {
                const fab = document.getElementById('fab-cart');
                if (fab) fab.style.display = (!isMobile() || !state.cart.length) ? 'none' : 'flex';
            });

            // ─── Format ───────────────────────────────────────────────────────────────
            function formatRp(n) {
                return 'Rp ' + Math.round(n).toLocaleString('id-ID');
            }

            // ─── Init ─────────────────────────────────────────────────────────────────
            loadProducts();
        </script>
    @endpush
@endsection

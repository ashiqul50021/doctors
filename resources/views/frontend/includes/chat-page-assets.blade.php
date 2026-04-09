<style>
    .chat-page-shell {
        background:
            radial-gradient(circle at top left, rgba(59, 130, 246, 0.08), transparent 28%),
            linear-gradient(180deg, #f8fbff 0%, #eef4fb 100%);
        min-height: calc(100vh - 120px);
        padding: 110px 0 48px;
    }

    .chat-page-shell .container,
    .chat-page-shell .container-fluid {
        max-width: 1480px;
        padding-left: 20px;
        padding-right: 20px;
    }

    .chat-page-shell .chat-window {
        display: grid;
        grid-template-columns: minmax(300px, 340px) minmax(0, 1fr);
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 28px;
        overflow: hidden;
        box-shadow: 0 22px 60px rgba(15, 23, 42, 0.08);
        min-height: calc(100vh - 220px);
    }

    .chat-page-shell .chat-window .chat-scroll {
        height: 100%;
        max-height: none;
        min-height: 0;
        overflow-y: auto;
        scrollbar-width: thin;
        scrollbar-color: #cbd5e1 transparent;
    }

    .chat-page-shell .chat-window .chat-scroll::-webkit-scrollbar {
        width: 8px;
    }

    .chat-page-shell .chat-window .chat-scroll::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 999px;
    }

    .chat-page-shell .chat-cont-left,
    .chat-page-shell .chat-cont-right {
        display: flex;
        flex-direction: column;
        min-height: 0;
        min-width: 0;
        max-width: none;
    }

    .chat-page-shell .chat-cont-left {
        border-right: 1px solid #e2e8f0;
        background: #fff;
    }

    .chat-page-shell .chat-cont-right {
        background: linear-gradient(180deg, #f8fbff 0%, #eef4fb 100%);
    }

    .chat-page-shell .chat-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        min-height: 88px;
        padding: 20px 24px;
        border-bottom: 1px solid #e2e8f0;
        background: rgba(255, 255, 255, 0.94);
        backdrop-filter: blur(10px);
    }

    .chat-page-shell .chat-cont-left .chat-header span {
        color: #0f172a;
        font-size: 28px;
        font-weight: 700;
        letter-spacing: -0.02em;
    }

    .chat-page-shell .chat-compose,
    .chat-page-shell .chat-options > a,
    .chat-page-shell .back-user-list {
        width: 44px;
        height: 44px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 14px;
        border: 1px solid #dbeafe;
        background: #eff6ff;
        color: #2563eb;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .chat-page-shell .chat-compose:hover,
    .chat-page-shell .chat-options > a:hover,
    .chat-page-shell .back-user-list:hover {
        background: #2563eb;
        border-color: #2563eb;
        color: #fff;
    }

    .chat-page-shell .chat-search {
        padding: 18px 20px;
        border-bottom: 1px solid #e2e8f0;
        background: #fff;
    }

    .chat-page-shell .chat-search .input-group,
    .chat-page-shell .chat-footer .input-group {
        display: flex;
        align-items: center;
        gap: 12px;
        width: 100%;
    }

    .chat-page-shell .chat-search .input-group {
        min-height: 56px;
        padding: 0 18px;
        border-radius: 18px;
        border: 1px solid #dbe5f3;
        background: #f8fbff;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.8);
    }

    .chat-page-shell .chat-search .input-group-prepend,
    .chat-page-shell .chat-search .input-group-append,
    .chat-page-shell .chat-footer .input-group-prepend,
    .chat-page-shell .chat-footer .input-group-append {
        display: flex;
        align-items: center;
        justify-content: center;
        position: static;
    }

    .chat-page-shell .chat-search .input-group-prepend {
        color: #64748b;
        pointer-events: none;
    }

    .chat-page-shell .chat-search .form-control,
    .chat-page-shell .chat-footer .form-control {
        flex: 1 1 auto;
        width: 1%;
        min-width: 0;
        border: 0;
        background: transparent;
        color: #0f172a;
        padding: 0;
        box-shadow: none;
        font-size: 15px;
    }

    .chat-page-shell .chat-search .form-control::placeholder,
    .chat-page-shell .chat-footer .form-control::placeholder {
        color: #94a3b8;
    }

    .chat-page-shell .chat-users-list,
    .chat-page-shell .chat-body {
        flex: 1 1 auto;
        min-height: 0;
        overflow: hidden;
    }

    .chat-page-shell .chat-users-list a.media,
    .chat-page-shell .chat-cont-right .chat-header .media,
    .chat-page-shell .chat-cont-right .chat-body .media {
        display: flex;
        text-decoration: none;
    }

    .chat-page-shell .chat-users-list a.media {
        align-items: flex-start;
        gap: 14px;
        padding: 18px 20px;
        border-bottom: 1px solid #eef2f7;
        transition: all 0.2s ease;
    }

    .chat-page-shell .chat-users-list a.media:last-child {
        border-bottom: 0;
    }

    .chat-page-shell .chat-users-list a.media:hover,
    .chat-page-shell .chat-users-list a.media.active {
        background: linear-gradient(180deg, #f8fbff 0%, #eff6ff 100%);
    }

    .chat-page-shell .chat-users-list .media-img-wrap {
        flex-shrink: 0;
        margin: 0;
    }

    .chat-page-shell .chat-users-list .avatar,
    .chat-page-shell .chat-cont-right .chat-header .avatar {
        width: 52px;
        height: 52px;
        overflow: visible;
    }

    .chat-page-shell .chat-cont-right .chat-body .avatar {
        width: 38px;
        height: 38px;
        flex-shrink: 0;
        margin-top: 4px;
    }

    .chat-page-shell .avatar .avatar-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 999px;
        border: 2px solid #fff;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.12);
    }

    .chat-page-shell .avatar-online,
    .chat-page-shell .avatar-away,
    .chat-page-shell .avatar-offline {
        position: relative;
    }

    .chat-page-shell .avatar-online::after,
    .chat-page-shell .avatar-away::after,
    .chat-page-shell .avatar-offline::after {
        content: "";
        position: absolute;
        right: 2px;
        bottom: 2px;
        width: 12px;
        height: 12px;
        border-radius: 999px;
        border: 2px solid #fff;
    }

    .chat-page-shell .avatar-online::after {
        background: #22c55e;
    }

    .chat-page-shell .avatar-away::after {
        background: #f59e0b;
    }

    .chat-page-shell .avatar-offline::after {
        background: #ef4444;
    }

    .chat-page-shell .chat-users-list .media-body {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        flex: 1 1 auto;
        min-width: 0;
    }

    .chat-page-shell .chat-users-list .media-body > div:first-child,
    .chat-page-shell .chat-cont-right .chat-header .media-body {
        min-width: 0;
    }

    .chat-page-shell .chat-users-list .user-name,
    .chat-page-shell .chat-cont-right .chat-header .user-name {
        color: #0f172a;
        font-weight: 700;
        line-height: 1.3;
    }

    .chat-page-shell .chat-users-list .user-name {
        margin-bottom: 4px;
        font-size: 16px;
    }

    .chat-page-shell .chat-users-list .user-last-chat {
        color: #64748b;
        font-size: 14px;
        line-height: 1.5;
        display: -webkit-box;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2;
        overflow: hidden;
    }

    .chat-page-shell .chat-users-list .media-body > div:last-child {
        flex-shrink: 0;
        text-align: right;
    }

    .chat-page-shell .last-chat-time {
        color: #94a3b8;
        font-size: 12px;
        font-weight: 600;
        display: block;
    }

    .chat-page-shell .badge.badge-success.badge-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 24px;
        height: 24px;
        padding: 0 8px;
        margin-top: 8px;
        border-radius: 999px;
        background: #22c55e;
        color: #fff;
        font-size: 12px;
        font-weight: 700;
    }

    .chat-page-shell .chat-cont-right .chat-header .media {
        align-items: center;
        gap: 14px;
        min-width: 0;
        flex: 1 1 auto;
    }

    .chat-page-shell .chat-cont-right .chat-header .user-name {
        font-size: 22px;
        margin-bottom: 4px;
    }

    .chat-page-shell .chat-cont-right .chat-header .user-status {
        color: #64748b;
        font-size: 14px;
    }

    .chat-page-shell .chat-options {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-shrink: 0;
    }

    .chat-page-shell .chat-body {
        padding: 24px;
    }

    .chat-page-shell .chat-body ul.list-unstyled {
        display: flex;
        flex-direction: column;
        gap: 18px;
        margin: 0;
        padding: 0;
    }

    .chat-page-shell .chat-body .media {
        align-items: flex-end;
        gap: 12px;
        max-width: 78%;
        margin: 0;
    }

    .chat-page-shell .chat-body .media.received {
        margin-right: auto;
    }

    .chat-page-shell .chat-body .media.sent {
        margin-left: auto;
        justify-content: flex-end;
    }

    .chat-page-shell .chat-body .media .media-body {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin: 0;
        min-width: 0;
    }

    .chat-page-shell .chat-body .media.sent .media-body {
        align-items: flex-end;
    }

    .chat-page-shell .chat-body .msg-box {
        margin: 0;
    }

    .chat-page-shell .chat-body .msg-box > div {
        display: inline-block;
        max-width: min(100%, 720px);
        padding: 16px 18px;
        border-radius: 20px;
        box-shadow: 0 16px 34px rgba(15, 23, 42, 0.06);
    }

    .chat-page-shell .chat-body .media.received .msg-box > div {
        background: #fff;
        border-top-left-radius: 8px;
    }

    .chat-page-shell .chat-body .media.sent .msg-box > div {
        background: #e8f0ff;
        border-top-right-radius: 8px;
    }

    .chat-page-shell .chat-body .msg-box > div p {
        color: #0f172a;
        margin: 0;
        line-height: 1.65;
    }

    .chat-page-shell .chat-msg-info {
        display: flex;
        align-items: center;
        gap: 10px;
        list-style: none;
        margin: 12px 0 0;
        padding: 0;
    }

    .chat-page-shell .chat-msg-info li,
    .chat-page-shell .chat-msg-info a,
    .chat-page-shell .chat-time {
        color: #64748b;
        font-size: 12px;
        text-decoration: none;
    }

    .chat-page-shell .chat-msg-attachments {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(128px, 1fr));
        gap: 10px;
    }

    .chat-page-shell .chat-attachment {
        position: relative;
        overflow: hidden;
        border-radius: 16px;
        min-width: 0;
    }

    .chat-page-shell .chat-attachment img {
        width: 100%;
        height: 120px;
        object-fit: cover;
        display: block;
    }

    .chat-page-shell .chat-attach-caption {
        position: absolute;
        left: 12px;
        right: 44px;
        bottom: 10px;
        color: #fff;
        font-size: 13px;
        font-weight: 600;
        text-shadow: 0 2px 10px rgba(15, 23, 42, 0.6);
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .chat-page-shell .chat-attach-download {
        position: absolute;
        right: 10px;
        bottom: 10px;
        width: 30px;
        height: 30px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        background: rgba(15, 23, 42, 0.6);
        color: #fff;
        text-decoration: none;
    }

    .chat-page-shell .chat-date {
        position: relative;
        display: block;
        margin: 8px 0;
        color: #64748b;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-align: center;
        text-transform: uppercase;
    }

    .chat-page-shell .chat-date::before,
    .chat-page-shell .chat-date::after {
        content: "";
        position: absolute;
        top: 50%;
        width: calc(50% - 54px);
        height: 1px;
        background: #cbd5e1;
    }

    .chat-page-shell .chat-date::before {
        left: 0;
    }

    .chat-page-shell .chat-date::after {
        right: 0;
    }

    .chat-page-shell .chat-msg-actions {
        position: absolute;
        top: 14px;
        right: 14px;
    }

    .chat-page-shell .chat-msg-actions .dropdown-menu {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 16px 40px rgba(15, 23, 42, 0.1);
    }

    .chat-page-shell .msg-typing {
        display: flex;
        align-items: center;
        gap: 4px;
        height: auto;
        padding-top: 2px;
    }

    .chat-page-shell .msg-typing span {
        float: none;
        width: 8px;
        height: 8px;
        margin: 0;
        border-radius: 999px;
        background: #94a3b8;
        animation: chatTyping 1s infinite ease-in-out;
    }

    .chat-page-shell .msg-typing span:nth-child(2) {
        animation-delay: 0.15s;
    }

    .chat-page-shell .msg-typing span:nth-child(3) {
        animation-delay: 0.3s;
    }

    @keyframes chatTyping {
        0%, 80%, 100% {
            transform: translateY(0);
            opacity: 0.45;
        }

        40% {
            transform: translateY(-3px);
            opacity: 1;
        }
    }

    .chat-page-shell .chat-footer {
        padding: 20px 24px 24px;
        border-top: 1px solid #e2e8f0;
        background: rgba(255, 255, 255, 0.92);
    }

    .chat-page-shell .chat-footer .input-group {
        min-height: 64px;
        padding: 8px 10px 8px 12px;
        border-radius: 22px;
        border: 1px solid #dbe5f3;
        background: #f8fbff;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.8);
    }

    .chat-page-shell .btn-file,
    .chat-page-shell .msg-send-btn {
        width: 46px;
        height: 46px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 16px;
        border: 1px solid #dbeafe;
        background: #fff;
        color: #2563eb;
    }

    .chat-page-shell .msg-send-btn {
        background: #2563eb;
        border-color: #2563eb;
        color: #fff;
        margin-left: 0;
        min-width: 46px;
        font-size: 18px;
    }

    .chat-page-shell .btn-file {
        position: relative;
        overflow: hidden;
    }

    .chat-page-shell .btn-file input[type="file"] {
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
    }

    .chat-page-shell .call-modal .modal-content {
        border: 0;
        border-radius: 24px;
        overflow: hidden;
    }

    @media (max-width: 991.98px) {
        .chat-page-shell {
            padding: 92px 0 28px;
        }

        .chat-page-shell .container,
        .chat-page-shell .container-fluid {
            padding-left: 14px;
            padding-right: 14px;
        }

        .chat-page-shell .chat-window {
            grid-template-columns: 1fr;
            min-height: calc(100vh - 150px);
        }

        .chat-page-shell .chat-cont-left,
        .chat-page-shell .chat-cont-right {
            max-width: 100%;
        }

        .chat-page-shell .chat-cont-left {
            display: flex;
        }

        .chat-page-shell .chat-cont-right {
            display: none;
        }

        .chat-page-shell .chat-window.chat-slide .chat-cont-left {
            display: none;
        }

        .chat-page-shell .chat-window.chat-slide .chat-cont-right {
            display: flex;
        }

        .chat-page-shell .chat-cont-right .chat-header .back-user-list {
            display: inline-flex;
        }

        .chat-page-shell .chat-cont-right .chat-header .media-body {
            display: block;
        }

        .chat-page-shell .chat-body .media {
            max-width: 92%;
        }
    }

    @media (min-width: 992px) {
        .chat-page-shell .chat-cont-right .chat-header .back-user-list {
            display: none;
        }
    }

    @media (max-width: 575.98px) {
        .chat-page-shell .chat-header,
        .chat-page-shell .chat-footer {
            padding-left: 16px;
            padding-right: 16px;
        }

        .chat-page-shell .chat-search,
        .chat-page-shell .chat-body {
            padding-left: 16px;
            padding-right: 16px;
        }

        .chat-page-shell .chat-users-list a.media {
            padding-left: 16px;
            padding-right: 16px;
        }

        .chat-page-shell .chat-cont-left .chat-header span {
            font-size: 24px;
        }

        .chat-page-shell .chat-cont-right .chat-header .user-name {
            font-size: 18px;
        }

        .chat-page-shell .chat-options {
            gap: 8px;
        }

        .chat-page-shell .chat-compose,
        .chat-page-shell .chat-options > a,
        .chat-page-shell .back-user-list,
        .chat-page-shell .btn-file,
        .chat-page-shell .msg-send-btn {
            width: 40px;
            height: 40px;
            border-radius: 14px;
        }

        .chat-page-shell .chat-body .media {
            max-width: 100%;
        }

        .chat-page-shell .chat-body .msg-box > div {
            width: 100%;
        }
    }
</style>

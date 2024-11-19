<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Fd-Borrow Welcome</title>
        <style>
            body {
                display: flex;
                align-items: center;
                background: #f9fcff;
                justify-content: center;
                & .content {
                    width: 60%;
                    padding: 32px;
                    background: #fff;
                    border-radius: 16px;
                    box-shadow: rgba(17, 17, 26, 0.1) 0px 4px 16px, rgba(17, 17, 26, 0.05) 0px 8px 32px;
                    & .title {
                        color: #007bff;
                        font-weight: bold;
                        font-size: clamp(16px, 2vw, 24px);
                    }
                    & .subtitle {
                        color: #949494;
                        margin-bottom: 16px;
                        font-size: clamp(15px, 1.5vw, 22px);
                    }
                    & .layout {
                        gap: 16px;
                        display: flex;
                        padding: 16px;
                        list-style: none;
                        font-weight: bold;
                        font-size: clamp(16px, 2vw, 24px);
                        & .btn-log {
                            border: none;
                            padding: 12px;
                            color: #007bff;
                            font-weight: bold;
                            text-align: center;
                            border-radius: 30px;
                            background: #f9fcff;
                            text-decoration: none;
                            font-size: clamp(16px, 2vw, 24px);
                            transition: all 0.1s ease-in-out;
                            box-shadow: rgb(204, 219, 232) 3px 3px 6px 0px inset, rgba(255, 255, 255, 0.5) -3px -3px 6px 1px inset;
                            & input {
                                border: none;
                                background: none;
                                color: #007bff;
                                font-weight: bold;
                                font-size: clamp(16px, 2vw, 24px);
                                &:hover {
                                    color: #fff;
                                    box-shadow: none;
                                    background: #007bff;
                                }
                            }
                            &:hover {
                                color: #fff;
                                box-shadow: none;
                                background: #007bff;
                            }
                        }
                    }
                }
            }
        </style>
    </head>
    <body>
        <div class="content">
            <h1 class="title">FD-BORROW</h1>
            <p class="subtitle">ระบบยืมคืนอุปกรณ์วิทยุสื่อสาร</p>
            <ul class="layout">
                <a href="{{ route('filament.admin.auth.login')}}" class="btn-log">Login</a>
                <form action="http://fdnet2.dhammakaya.network:8001/logout" method="post" class="btn-log">
                    <input type="submit" value="Logout"/>
                </form>
            </ul>
        </div>
    </body>
</html>
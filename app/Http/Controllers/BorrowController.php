<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\EmailService;
use Filament\Facades\Filament;
use App\Models\Main\BorrowHead;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use App\Http\Controllers\HRController;

class BorrowController extends Controller {
    public function genDataMail(BorrowHead $borrowHead) {
        $borrowItems = $borrowHead->borrowitems;
        $url = route('borrow.access', [
            'token' => Crypt::encrypt([
                'borrowId' => $borrowHead->id,
                'qhead' => $borrowHead->qhead
            ])
        ]);
        $index = 0;
        $tableBorrowItem = '
            <table style="max-width:600px;width:100%;overflow:hidden;border-radius:10px;border-collapse:collapse;position:relative;">
                <thead>
                    <tr style="height:50px;background:#36304a;font-size:14px;">
                        <th style="padding:5px;text-align:center;color:#fff;line-height:1.2;font-weight:unset;">ลำดับ</th>
                        <th style="padding:5px;text-align:left;color:#fff;line-height:1.2;font-weight:unset;">รายละเอียดอุปกรณ์</th>
                        <th style="padding:5px;text-align:center;color:#fff;line-height:1.2;font-weight:unset;">จำนวนที่ขอยืม</th>
                    </tr>
                </thead>
                <tbody>
        ';
        foreach($borrowItems as $rsItem) {
            $bg_tr = ($index % 2 === 0) ? '#f7f7f7' :'#caf0f8';
            $tableBorrowItem .= '
                    <tr style="height:40px;font-size:14px;line-height:1.1;font-weight:unset;background:'.$bg_tr.';">
                        <td style="text-align:center;">'.($index + 1).'.</td>
                        <td style="text-align:left;padding-left:5px;color:#000;">'.$rsItem->product->category->name.' '.$rsItem->product->name.'</td>
                        <td style="text-align:center;color:blue;font-weight:800;">'.$rsItem->q_request.'</td>
                    </tr>
            ';
            $index++;
        }
        $tableBorrowItem .= '
                </tbody>
            </table>
        ';

        if($borrowHead->status_id == 8) {
            $approveMail = 'layitsnew8503@gmail.com';
            $subject = 'กรุณาพิจารณาอนุมัติยืมวิทยุสื่อสาร : BID-'.$borrowHead->id;
            $message = '
                <html><body>
                    <p style="font-size:14px;font-weight:800;">เรียน : <span style="color:blue;">'.$borrowHead->chead.'</span></p>
                    <p style="font-size:14px;font-weight:800;">เรื่อง : พิจารณาอนุมัติยืมวิทยุสื่อสาร จากบุคลากรในหน่วยงานของท่าน</p>
                    <p style="font-size:14px;font-weight:800;">ชื่อผู้ขอ : <span style="color:blue;">'.$borrowHead->borrower->fullname.'</span></p>
                    <p style="font-size:14px;font-weight:800;">หน่วยงาน : '.$borrowHead->samnak->csamnak.' '.$borrowHead->kong->ckong.'</p>
                    <br>
                    <p style="font-size:14px;font-weight:800;">ชื่อกิจกรรม : '.$borrowHead->activity_name.'</p>
                    <p style="font-size:14px;font-weight:800;">สถานที่ใช้งาน : '.$borrowHead->activity_place.'</p>
                    <br>'.$tableBorrowItem.'<br>
                    <a href="'.$url.'" style="padding:8px 16px;border-radius:8px;font-size:16px;background:#03045e;color:#fff;text-decoration:none;">ตรวจสอบข้อมูลยืมเพื่ออนุมัติ</a>
                    <br>
                </body></html>
            ';
            $emailService = app(EmailService::class);
            $responseMail = $emailService->sendEmail($approveMail, $subject, $message);
            return Log::info([$responseMail, $url]);
        }
    }

    public function accessWithToken(Request $request) {
        try {
            $token = $request->query('token');
            if(!$token) {
                abort(400, 'Token is missing.');
            }
            $data = Crypt::decrypt($token);
            $aduser = $data['qhead'] ?? null;
            (new HRController())->fetchAndStoreUserData($aduser);
            $userData = session('user_data');
            if(!$userData) {
                throw new \Exception('User data is missing.');
            }
            $approver = User::updateOrCreate(['name' => $userData['aduser']], [
                'email' => $userData['email'],
                'fullname' => $userData['fullname'],
                'password' => ''
            ]);
            $roleId = 3;
            $approver->roles()->syncWithoutDetaching([$roleId]);
            Filament::auth()->login($approver);
            return redirect()->to(route('filament.admin.resources.main.borrows.edit', ['record' => $data['borrowId']]));
        } catch(\Exception $e) {
            Log::error($e->getMessage());
            abort(500, 'An unexpected error occured'());
        }
    }
}
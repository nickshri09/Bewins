<?php

namespace App\Services;

use App\Models\Investment;
use App\Models\InvestmentAgreementDocument;
use App\Models\Loan;
use App\Models\LoanAgreementDocument;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class AgreementEmailService
{
    public function sendInvestmentAgreement(Investment $investment): array
    {
        $investment->loadMissing(['member', 'agreement_documents']);

        if ((int) $investment->status !== 1) {
            return ['sent' => false, 'message' => 'The investment is not approved yet.'];
        }

        if ($investment->agreement_email_sent_at) {
            return ['sent' => true, 'message' => 'The agreement email was already sent.'];
        }

        if (blank($investment->member?->email)) {
            return ['sent' => false, 'message' => 'The client profile has no registered email address.'];
        }

        try {
            $filename = 'bcs-investment-agreement-'.$investment->getKey().'.pdf';
            $directory = public_path('uploads/investments');
            $path = $directory.'/'.$filename;

            if (!is_file($path)) {
                File::ensureDirectoryExists($directory);

                $customer = $investment->member;
                $customerName = trim(($customer->first_name ?? '').' '.($customer->last_name ?? ''));
                $customerAddress = collect([
                    $customer->address ?? null,
                    $customer->city ?? null,
                    $customer->state ?? null,
                    $customer->zip ?? null,
                ])->filter()->implode(', ');
                $netInterest = (float) $investment->wallet_transactions()
                    ->where('type', 'credit')
                    ->sum('amount');
                $tds = round($netInterest * 0.10, 2);
                $investmentCode = $investment->investment_code ?: '#'.$investment->id;
                $contributionType = match (strtolower((string) $investment->contribution_type)) {
                    'onetime' => 'One-time',
                    'monthly' => 'Monthly',
                    default => ucfirst((string) $investment->contribution_type),
                };

                $pdf = Pdf::loadView('investments.agreement_pdf', [
                    'agreement_id' => 'BCS-AG-'.str_pad((string) $investment->id, 6, '0', STR_PAD_LEFT),
                    'investment_code' => $investmentCode,
                    'agreement_date' => now()->format('d-m-Y'),
                    'customer_name' => $customerName ?: 'Customer',
                    'customer_id' => $customer->member_no ?: (string) $customer->id,
                    'customer_email' => $customer->email,
                    'customer_address' => $customerAddress,
                    'investment_amount' => number_format((float) $investment->investment_amount, 2),
                    'contribution_type' => $contributionType,
                    'investment_date' => $investment->start_date ? $investment->start_date->format('d-m-Y') : '—',
                    'applicable_tds' => number_format($tds, 2),
                    'applicable_charges' => number_format(0, 2),
                    'total_amount' => number_format((float) $investment->investment_amount + $netInterest - $tds, 2),
                    'investment_status' => $investment->statusLabel,
                    'company_address' => get_option('address') ?: '',
                    'support_email' => get_option('email') ?: '',
                ])->setPaper('a4');

                File::put($path, $pdf->output());
            }

            $document = InvestmentAgreementDocument::firstOrNew([
                'investment_id' => $investment->getKey(),
                'file' => $filename,
            ]);
            $document->name = 'BCS Investment Agreement';
            $document->save();
            $investment->load('agreement_documents');
        } catch (Throwable $exception) {
            $investment->agreement_email_error = mb_substr($exception->getMessage(), 0, 1000);
            $investment->save();
            Log::error('Could not generate BCS investment agreement.', [
                'investment_id' => $investment->getKey(),
                'exception' => $exception->getMessage(),
            ]);

            return ['sent' => false, 'message' => 'The BCS agreement PDF could not be generated. Check the application logs.'];
        }

        return $this->send(
            $investment,
            $investment->member?->email,
            $investment->agreement_documents->where('file', 'bcs-investment-agreement-'.$investment->getKey().'.pdf'),
            'uploads/investments',
            'BCS Investment Agreement',
            $investment->investment_code ?: ('#'.$investment->id)
        );
    }

    public function sendLoanAgreement(Loan $loan): array
    {
        $loan->loadMissing(['borrower', 'loan_product', 'repayments', 'collaterals', 'agreement_documents']);

        if ((int) $loan->status !== 1) {
            return ['sent' => false, 'message' => 'The loan is not approved yet.'];
        }

        if ($loan->agreement_email_sent_at) {
            return ['sent' => true, 'message' => 'The agreement email was already sent.'];
        }

        if (blank($loan->borrower?->email)) {
            return ['sent' => false, 'message' => 'The borrower profile has no registered email address.'];
        }

        try {
            $filename = 'loan-agreement-'.$loan->getKey().'.pdf';
            $directory = public_path('uploads/agreements');
            $path = $directory.'/'.$filename;

            if (!is_file($path)) {
                File::ensureDirectoryExists($directory);

                $borrower = $loan->borrower;
                $borrowerName = trim(($borrower->first_name ?? '').' '.($borrower->last_name ?? ''));
                $borrowerAddress = collect([
                    $borrower->address ?? null,
                    $borrower->city ?? null,
                    $borrower->state ?? null,
                    $borrower->zip ?? null,
                ])->filter()->implode(', ');
                $repayments = $loan->repayments->sortBy('id')->values();
                $firstRepayment = $repayments->first();
                $lastRepayment = $repayments->last();
                $collateralDetails = $loan->collaterals->map(function ($collateral) {
                    return trim(collect([$collateral->name, $collateral->collateral_type, $collateral->description])
                        ->filter()->implode(' — '));
                })->filter()->implode('; ');
                $agreementId = 'BLN-AG-'.str_pad((string) $loan->id, 6, '0', STR_PAD_LEFT);
                $loanCode = $loan->loan_id ?: '#'.$loan->id;
                $termPeriod = strtolower((string) $loan->loan_product->term_period);
                $frequency = str_contains($termPeriod, 'month') ? 'Monthly'
                    : (str_contains($termPeriod, 'week') ? 'Weekly'
                    : (str_contains($termPeriod, 'year') ? 'Yearly'
                    : (str_contains($termPeriod, 'day') ? 'Daily' : 'As per repayment schedule')));

                $pdf = Pdf::loadView('loans.agreement_pdf', [
                    'agreement_id' => $agreementId,
                    'loan_id' => $loanCode,
                    'agreement_date' => now()->format('d-m-Y'),
                    'lender_name' => get_option('company_name') ?: 'Not recorded in the loan record',
                    'lender_address' => get_option('address') ?: 'Not recorded in the loan record',
                    'platform_name' => 'Bewins Services Private Limited',
                    'borrower_name' => $borrowerName ?: 'Borrower',
                    'customer_id' => $borrower->member_no ?: (string) $borrower->id,
                    'borrower_email' => $borrower->email,
                    'borrower_address' => $borrowerAddress ?: 'Not provided',
                    'loan_type' => $loan->loan_product->name ?: 'Not specified',
                    'loan_purpose' => $loan->description ?: 'Not specified',
                    'sanctioned_amount' => number_format((float) $loan->applied_amount, 2),
                    'disbursement_amount' => $loan->disbursed_at
                        ? number_format((float) $loan->applied_amount, 2)
                        : 'Pending disbursement confirmation',
                    'interest_rate' => number_format((float) $loan->interest_rate, 2),
                    'processing_fee' => 'Not recorded',
                    'applicable_taxes' => 'Not recorded',
                    'repayment_frequency' => $frequency,
                    'installment_count' => $repayments->count() ?: 'Not available',
                    'monthly_emi' => $firstRepayment ? number_format((float) $firstRepayment->amount_to_pay, 2) : 'Not available',
                    'first_payment_date' => $firstRepayment ? \Carbon\Carbon::parse($firstRepayment->getRawOriginal('repayment_date'))->format('d-m-Y') : 'Not available',
                    'final_payment_date' => $lastRepayment ? \Carbon\Carbon::parse($lastRepayment->getRawOriginal('repayment_date'))->format('d-m-Y') : 'Not available',
                    'collateral_details' => $collateralDetails ?: 'Not applicable / not recorded',
                    'loan_status' => 'Approved',
                    'support_email' => get_option('email') ?: 'Not recorded',
                ])->setPaper('a4');

                File::put($path, $pdf->output());
            }

            $document = LoanAgreementDocument::firstOrNew([
                'loan_id' => $loan->getKey(),
                'file' => $filename,
            ]);
            $document->name = 'Loan Agreement';
            $document->save();
            $loan->load('agreement_documents');
        } catch (Throwable $exception) {
            $loan->agreement_email_error = mb_substr($exception->getMessage(), 0, 1000);
            $loan->save();
            Log::error('Could not generate loan agreement.', [
                'loan_id' => $loan->getKey(),
                'exception' => $exception->getMessage(),
            ]);

            return ['sent' => false, 'message' => 'The loan agreement PDF could not be generated. Check the application logs.'];
        }

        return $this->send(
            $loan,
            $loan->borrower?->email,
            $loan->agreement_documents->where('file', 'loan-agreement-'.$loan->getKey().'.pdf'),
            'uploads/agreements',
            'Loan Agreement',
            $loan->loan_id ?: ('#'.$loan->id)
        );
    }

    private function send($record, ?string $email, $documents, string $directory, string $label, string $reference): array
    {
        if ((int) $record->status !== 1) {
            return ['sent' => false, 'message' => 'The record is not approved yet.'];
        }

        if ($record->agreement_email_sent_at) {
            return ['sent' => true, 'message' => 'The agreement email was already sent.'];
        }

        if (blank($email)) {
            return ['sent' => false, 'message' => 'The client profile has no registered email address.'];
        }

        if ($documents->isEmpty()) {
            return ['sent' => false, 'message' => 'Upload the agreement document before it can be emailed.'];
        }

        $attachments = [];
        foreach ($documents as $document) {
            $path = public_path($directory.'/'.$document->file);
            if (!is_file($path)) {
                return ['sent' => false, 'message' => 'An uploaded agreement file could not be found on the server.'];
            }

            $attachments[] = [
                'path' => $path,
                'name' => $this->attachmentName($document->name, $path),
            ];
        }

        try {
            Mail::raw(
                'Your '.$label.' for '.$reference.' is attached to this email. You can download and keep the attached document for your records.',
                function ($message) use ($email, $label, $reference, $attachments) {
                    $message->to($email)
                        ->subject($label.' - '.$reference);

                    foreach ($attachments as $attachment) {
                        $message->attach($attachment['path'], ['as' => $attachment['name']]);
                    }
                }
            );

            $record->agreement_email_sent_at = now();
            $record->agreement_email_error = null;
            $record->save();

            return ['sent' => true, 'message' => 'Agreement emailed to '.$email.'.'];
        } catch (Throwable $exception) {
            $record->agreement_email_error = mb_substr($exception->getMessage(), 0, 1000);
            $record->save();
            Log::error('Could not email approved '.$label.'.', [
                'record_id' => $record->getKey(),
                'email' => $email,
                'exception' => $exception->getMessage(),
            ]);

            return ['sent' => false, 'message' => 'The agreement email could not be sent. Check mail settings and retry the agreement email.'];
        }
    }

    private function attachmentName(?string $name, string $path): string
    {
        $name = trim((string) $name) ?: 'Agreement';
        $extension = pathinfo($path, PATHINFO_EXTENSION);

        if ($extension && strtolower(pathinfo($name, PATHINFO_EXTENSION)) !== strtolower($extension)) {
            $name .= '.'.$extension;
        }

        return $name;
    }
}

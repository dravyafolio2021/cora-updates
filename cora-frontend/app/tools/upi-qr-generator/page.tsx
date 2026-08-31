'use client';

import React, { useState } from 'react';
import Link from 'next/link';
import { ArrowLeft, QrCode, Copy, Check, Sparkles, ArrowRight, ShieldCheck, Download } from 'lucide-react';
import { useToast } from '@/components/ui/Toast';

export default function UpiQrGeneratorPage() {
  const [vpa, setVpa] = useState<string>('business@okhdfcbank');
  const [payeeName, setPayeeName] = useState<string>('Studio Services Private Limited');
  const [amount, setAmount] = useState<number>(25000);
  const [invoiceNote, setInvoiceNote] = useState<string>('INV-2026-042');
  const [copied, setCopied] = useState<boolean>(false);
  const { showToast } = useToast();

  const upiIntentUrl = `upi://pay?pa=${encodeURIComponent(vpa)}&pn=${encodeURIComponent(payeeName)}&am=${amount}&cu=INR&tn=${encodeURIComponent(invoiceNote)}`;
  const qrImageUrl = `https://api.qrserver.com/v1/create-qr-code/?size=260x260&data=${encodeURIComponent(upiIntentUrl)}&color=09090b`;

  const copyUpiLink = () => {
    navigator.clipboard.writeText(upiIntentUrl);
    setCopied(true);
    showToast('UPI payment intent link copied to clipboard!');
    setTimeout(() => setCopied(false), 2000);
  };

  return (
    <div className="py-12 md:py-20 bg-white min-h-screen">
      <div className="w-full max-w-[960px] mx-auto px-4 sm:px-6">
        
        {/* Back navigation */}
        <Link
          href="/tools"
          className="inline-flex items-center gap-1.5 text-xs font-semibold text-zinc-500 hover:text-zinc-950 mb-8 transition-colors"
        >
          <ArrowLeft className="w-4 h-4" /> Back to all micro-tools
        </Link>

        {/* Tool Header */}
        <div className="mb-10">
          <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-50 text-teal-800 border border-teal-200 text-[11px] font-mono font-bold mb-3">
            <QrCode className="w-3.5 h-3.5" />
            <span>0% Gateway Fees • Direct Bank Settlement</span>
          </div>
          <h1 className="font-display text-3xl sm:text-4xl md:text-5xl font-bold tracking-tight text-zinc-950 mb-3">
            Dynamic UPI QR &amp; Payment Generator
          </h1>
          <p className="text-sm sm:text-base text-zinc-600 max-w-[700px] leading-relaxed">
            Create instant UPI payment QR codes and deep links pre-filled with exact invoice amounts. Compatible with Google Pay, PhonePe, Paytm, and BHIM.
          </p>
        </div>

        {/* Main Grid: Form & Live QR Code */}
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
          
          {/* Controls Form (Left) */}
          <div className="lg:col-span-7 bg-white border border-zinc-200 rounded-3xl p-6 sm:p-8 shadow-sm space-y-5">
            <h3 className="text-xs font-mono font-bold uppercase tracking-wider text-zinc-500 pb-2 border-b border-zinc-100">
              Payment Parameters
            </h3>

            <div>
              <label className="text-xs font-bold text-zinc-800 block mb-1">Payee UPI ID (VPA)</label>
              <input
                type="text"
                value={vpa}
                onChange={(e) => setVpa(e.target.value)}
                placeholder="username@okhdfcbank"
                className="w-full px-3.5 py-2.5 rounded-xl border border-zinc-200 text-xs font-mono text-zinc-900 focus:outline-none focus:border-zinc-950"
              />
            </div>

            <div>
              <label className="text-xs font-bold text-zinc-800 block mb-1">Payee Business Name</label>
              <input
                type="text"
                value={payeeName}
                onChange={(e) => setPayeeName(e.target.value)}
                className="w-full px-3.5 py-2.5 rounded-xl border border-zinc-200 text-xs font-mono text-zinc-900 focus:outline-none focus:border-zinc-950"
              />
            </div>

            <div className="grid grid-cols-2 gap-3">
              <div>
                <label className="text-xs font-bold text-zinc-800 block mb-1">Invoice Amount (INR)</label>
                <input
                  type="number"
                  value={amount}
                  onChange={(e) => setAmount(Number(e.target.value))}
                  className="w-full px-3.5 py-2.5 rounded-xl border border-zinc-200 text-xs font-mono text-zinc-900 focus:outline-none focus:border-zinc-950"
                />
              </div>
              <div>
                <label className="text-xs font-bold text-zinc-800 block mb-1">Invoice Reference / Note</label>
                <input
                  type="text"
                  value={invoiceNote}
                  onChange={(e) => setInvoiceNote(e.target.value)}
                  className="w-full px-3.5 py-2.5 rounded-xl border border-zinc-200 text-xs font-mono text-zinc-900 focus:outline-none focus:border-zinc-950"
                />
              </div>
            </div>

            <div className="p-4 rounded-2xl bg-zinc-50 border border-zinc-200/80 text-xs text-zinc-700 space-y-1.5">
              <div className="flex items-center gap-1.5 font-bold text-zinc-900">
                <ShieldCheck className="w-4 h-4 text-emerald-600" />
                <span>Zero Intermediary Deductions</span>
              </div>
              <p className="text-[11.5px] text-zinc-500 leading-relaxed">
                UPI transactions route directly into your bank account. No 2-3% payment gateway commission fees.
              </p>
            </div>
          </div>

          {/* QR Code & Payment Summary (Right) */}
          <div className="lg:col-span-5 bg-zinc-950 text-white rounded-3xl p-6 sm:p-7 shadow-xl flex flex-col items-center justify-between text-center space-y-5">
            
            <div className="w-full pb-3 border-b border-zinc-800 flex items-center justify-between text-xs font-mono">
              <span className="text-zinc-400">Scan to Pay</span>
              <span className="text-emerald-400 font-bold">₹{amount.toLocaleString('en-IN')}</span>
            </div>

            {/* QR Card Container */}
            <div className="bg-white p-4 rounded-2xl shadow-md">
              <img
                src={qrImageUrl}
                alt="Dynamic UPI QR Code"
                className="w-48 h-48 object-contain rounded-lg"
              />
            </div>

            <div className="space-y-1">
              <span className="font-mono text-xs font-bold text-white block">{payeeName}</span>
              <span className="font-mono text-[11px] text-zinc-400 block">{vpa}</span>
            </div>

            {/* Actions */}
            <div className="w-full space-y-2.5 pt-2">
              <button
                onClick={copyUpiLink}
                className="w-full py-3 rounded-xl bg-white hover:bg-zinc-100 text-zinc-950 font-semibold text-xs transition-all flex items-center justify-center gap-2 cursor-pointer shadow-sm"
              >
                {copied ? <Check className="w-4 h-4 text-emerald-600" /> : <Copy className="w-4 h-4" />}
                <span>{copied ? 'UPI Link Copied!' : 'Copy UPI Intent Link'}</span>
              </button>

              <a
                href={upiIntentUrl}
                className="w-full py-2.5 rounded-xl bg-zinc-900 hover:bg-zinc-800 text-white font-semibold text-xs border border-zinc-700 transition-all flex items-center justify-center gap-1.5"
              >
                <span>Open in UPI App (Mobile)</span>
                <ArrowRight className="w-3.5 h-3.5 text-zinc-400" />
              </a>
            </div>

          </div>

        </div>

      </div>
    </div>
  );
}

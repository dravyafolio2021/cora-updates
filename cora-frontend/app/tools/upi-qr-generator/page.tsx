'use client';

import React, { useState } from 'react';
import Image from 'next/image';
import { QrCode, Copy, Check, Download, ShieldCheck } from 'lucide-react';
import { ToolPageShell } from '@/components/tools/ToolPageShell';
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

  const upiFaqs = [
    {
      question: 'Are there any transaction fees on UPI QR payments?',
      answer: 'No. Person-to-Merchant (P2M) and Person-to-Person (P2P) bank account UPI transfers carry 0% transaction fees under NPCI guidelines, settling directly into your linked current account.'
    },
    {
      question: 'Which UPI apps support these dynamic QR codes?',
      answer: 'All standard NPCI UPI apps support dynamic intent QR codes, including Google Pay, PhonePe, Paytm, BHIM, CRED, and all major Indian banking mobile apps.'
    },
    {
      question: 'Can clients pay with credit cards via UPI?',
      answer: 'Yes, if the client has linked a RuPay credit card to their UPI app, they can seamlessly scan and settle payments directly via UPI rail.'
    }
  ];

  return (
    <ToolPageShell
      toolId="upi-qr-generator"
      badgeTag="⚡ NPCI UPI 2.0 Standard"
      title="Dynamic UPI QR & Payment Generator"
      subtitle="Create instant UPI payment QR codes and deep links pre-filled with exact invoice amounts. Compatible with Google Pay, PhonePe, Paytm, and BHIM."
      faqItems={upiFaqs}
    >
      {/* ── 70% Tool Workspace (Interactive Split Form + QR Card) ── */}
      <div className="grid grid-cols-1 md:grid-cols-12 gap-6 items-stretch">
        
        {/* Left Console (7 Cols) */}
        <div className="md:col-span-7 bg-white border border-zinc-200/90 rounded-3xl p-5 sm:p-6 shadow-xs space-y-4">
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

          <div>
            <label className="text-xs font-bold text-zinc-800 block mb-1">Invoice Amount (₹)</label>
            <input
              type="number"
              value={amount}
              onChange={(e) => setAmount(Math.max(0, Number(e.target.value)))}
              className="w-full px-3.5 py-2.5 rounded-xl border border-zinc-200 text-xs font-mono text-zinc-900 focus:outline-none focus:border-zinc-950"
            />
          </div>

          <div>
            <label className="text-xs font-bold text-zinc-800 block mb-1">Transaction Note / Invoice Ref</label>
            <input
              type="text"
              value={invoiceNote}
              onChange={(e) => setInvoiceNote(e.target.value)}
              className="w-full px-3.5 py-2.5 rounded-xl border border-zinc-200 text-xs font-mono text-zinc-900 focus:outline-none focus:border-zinc-950"
            />
          </div>
        </div>

        {/* Right Output QR Canvas (5 Cols) */}
        <div className="md:col-span-5 bg-white border border-zinc-200/90 rounded-3xl p-5 sm:p-6 shadow-xs flex flex-col justify-between items-center text-center">
          <div className="w-full">
            <span className="text-[11px] font-mono text-zinc-500 uppercase tracking-wider block mb-3 font-semibold">
              Live Dynamic QR
            </span>
            
            {/* QR Canvas */}
            <div className="p-4 bg-zinc-50 rounded-2xl border border-zinc-200 inline-block mb-3">
              <img
                src={qrImageUrl}
                alt="UPI QR Code"
                width={180}
                height={180}
                className="rounded-lg mx-auto"
              />
            </div>

            <div className="font-mono text-lg font-bold text-zinc-950">
              ₹{amount.toLocaleString('en-IN')}
            </div>
            <div className="text-[11px] text-zinc-500 font-mono truncate max-w-[200px] mx-auto">
              {vpa}
            </div>
          </div>

          <div className="w-full space-y-2 mt-4 pt-4 border-t border-zinc-100">
            <button
              type="button"
              onClick={copyUpiLink}
              className="w-full py-2.5 px-4 rounded-xl bg-zinc-950 hover:bg-zinc-800 text-white font-bold text-xs flex items-center justify-center gap-2 transition-all cursor-pointer shadow-xs"
            >
              {copied ? <Check className="w-3.5 h-3.5 text-emerald-400" /> : <Copy className="w-3.5 h-3.5 text-zinc-400" />}
              <span>{copied ? 'Copied Deep-Link!' : 'Copy UPI Intent Link'}</span>
            </button>

            <a
              href={qrImageUrl}
              download="cora-upi-qr.png"
              target="_blank"
              rel="noopener noreferrer"
              className="w-full py-2.5 px-4 rounded-xl bg-zinc-50 hover:bg-zinc-100 border border-zinc-200 text-zinc-800 font-bold text-xs flex items-center justify-center gap-2 transition-all"
            >
              <Download className="w-3.5 h-3.5 text-zinc-500" />
              <span>Download QR Code Image</span>
            </a>
          </div>
        </div>

      </div>
    </ToolPageShell>
  );
}

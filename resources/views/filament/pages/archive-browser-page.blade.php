<x-filament-panels::page>
    @php
        $records = $this->getArchiveRecords();
        $selected = $this->selectedRecord;
    @endphp

    <div style="margin-bottom:1rem;padding:1rem;border:1px solid #dbe4f0;border-radius:12px;background:#fff;">
        <div style="display:flex;gap:1rem;flex-wrap:wrap;align-items:end;">
            <div style="flex:1;min-width:220px;">
                <label style="display:block;font-size:.78rem;color:#6b7280;text-transform:uppercase;letter-spacing:.08em;font-weight:700;margin-bottom:.35rem;">Search Archive</label>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search ticket code, provider ref, message, holder name..."
                    style="width:100%;border:1px solid #cbd5e1;border-radius:.6rem;padding:.6rem .75rem;font-size:.92rem;"
                />
            </div>

            <div style="width:220px;">
                <label style="display:block;font-size:.78rem;color:#6b7280;text-transform:uppercase;letter-spacing:.08em;font-weight:700;margin-bottom:.35rem;">Record Type</label>
                <select wire:model.live="recordType" style="width:100%;border:1px solid #cbd5e1;border-radius:.6rem;padding:.6rem .75rem;font-size:.92rem;">
                    <option value="all">All</option>
                    <option value="payments">Payments</option>
                    <option value="tickets">Tickets</option>
                    <option value="errors">Errors</option>
                </select>
            </div>

            <div>
                <button type="button" wire:click="clearSelection" style="background:#1e293b;color:#fff;border:none;padding:.65rem 1rem;border-radius:.6rem;font-weight:700;cursor:pointer;">
                    Clear Selection
                </button>
            </div>
        </div>

        <div style="margin-top:.85rem;color:#475569;font-size:.88rem;">
            This page reads from the archive database only. The live database is not touched.
        </div>
    </div>

    <div style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;overflow:auto;">
        <table style="width:100%;border-collapse:collapse;min-width:780px;">
            <thead>
                <tr style="background:#f8fafc;">
                    <th style="text-align:left;padding:.75rem .9rem;font-size:.74rem;color:#475569;text-transform:uppercase;letter-spacing:.08em;">Type</th>
                    <th style="text-align:left;padding:.75rem .9rem;font-size:.74rem;color:#475569;text-transform:uppercase;letter-spacing:.08em;">Title</th>
                    <th style="text-align:left;padding:.75rem .9rem;font-size:.74rem;color:#475569;text-transform:uppercase;letter-spacing:.08em;">Status</th>
                    <th style="text-align:left;padding:.75rem .9rem;font-size:.74rem;color:#475569;text-transform:uppercase;letter-spacing:.08em;">Meta</th>
                    <th style="text-align:left;padding:.75rem .9rem;font-size:.74rem;color:#475569;text-transform:uppercase;letter-spacing:.08em;">Created</th>
                    <th style="text-align:right;padding:.75rem .9rem;font-size:.74rem;color:#475569;text-transform:uppercase;letter-spacing:.08em;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($records as $record)
                    <tr style="border-top:1px solid #f1f5f9;">
                        <td style="padding:.8rem .9rem;font-weight:700;color:#0f172a;text-transform:uppercase;">{{ $record['source'] }}</td>
                        <td style="padding:.8rem .9rem;color:#0f172a;">
                            <button type="button" wire:click="openRecord('{{ $record['source'] }}', {{ (int) $record['id'] }})" style="background:none;border:none;padding:0;color:#0a4fbe;font-weight:700;text-decoration:underline;cursor:pointer;text-align:left;">
                                {{ $record['title'] }}
                            </button>
                            <div style="font-size:.8rem;color:#6b7280;margin-top:.15rem;">{{ $record['subtitle'] }}</div>
                        </td>
                        <td style="padding:.8rem .9rem;color:#334155;">{{ $record['status'] }}</td>
                        <td style="padding:.8rem .9rem;color:#334155;">{{ $record['meta'] }}</td>
                        <td style="padding:.8rem .9rem;color:#334155;">{{ $record['created_at'] }}</td>
                        <td style="padding:.8rem .9rem;text-align:right;">
                            <button type="button" wire:click="openRecord('{{ $record['source'] }}', {{ (int) $record['id'] }})" style="background:#1e293b;color:#fff;border:none;padding:.45rem .75rem;border-radius:.5rem;font-size:.82rem;font-weight:700;cursor:pointer;">
                                View
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="padding:1.4rem .9rem;text-align:center;color:#64748b;">
                            No archive records found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($selected)
        <div style="margin-top:1.25rem;background:#fff;border:1px solid #dbe4f0;border-radius:12px;padding:1rem;">
            <div style="display:flex;gap:1rem;flex-wrap:wrap;justify-content:space-between;align-items:flex-start;">
                <div>
                    <div style="font-size:.78rem;color:#6b7280;text-transform:uppercase;letter-spacing:.08em;font-weight:700;">Selected Record</div>
                    <div style="font-size:1rem;color:#0f172a;font-weight:800;">{{ strtoupper($selected['source']) }} #{{ $selected['id'] }}</div>
                </div>
            </div>

            <div style="margin-top:1rem;">
                <div style="font-size:.78rem;color:#6b7280;text-transform:uppercase;letter-spacing:.08em;font-weight:700;margin-bottom:.45rem;">Record Data</div>
                <pre style="white-space:pre-wrap;word-break:break-word;background:#0f172a;color:#e2e8f0;padding:1rem;border-radius:10px;max-height:520px;overflow:auto;font-size:.82rem;line-height:1.45;">{{ $selected['pretty'] }}</pre>
            </div>
        </div>
    @endif
</x-filament-panels::page>

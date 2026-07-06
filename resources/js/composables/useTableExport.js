import * as XLSX from 'xlsx'
import html2pdf from 'html2pdf.js'
import JSZip from 'jszip'
import { saveAs } from 'file-saver'

import {
    Document,
    Packer,
    Paragraph,
    Table,
    TableCell,
    TableRow,
    WidthType,
    PageOrientation,
    TextRun,
} from 'docx'

export function useTableExport() {
    const currentDateTime = () => {
        return new Date().toISOString().slice(0, 19).replace(/:/g, '-')
    }

    const formatValue = (value) => {
        if (value === null || value === undefined) return ''

        if (typeof value === 'boolean') {
            return value ? 'true' : 'false'
        }

        if (Array.isArray(value)) {
            return value.join(', ')
        }

        if (typeof value === 'object') {
            return JSON.stringify(value)
        }

        return String(value)
    }

    const formatData = (items, columns) => {
        return items.map((item) => {
            const row = {}

            columns.forEach((column) => {
                row[column] = formatValue(item[column])
            })

            return row
        })
    }

    const downloadCSV = (items, columns, filename) => {
        const worksheet = XLSX.utils.json_to_sheet(formatData(items, columns))
        const csvOutput = XLSX.utils.sheet_to_csv(worksheet)
        const blob = new Blob(['\uFEFF' + csvOutput], {
            type: 'text/csv;charset=utf-8;',
        })

        saveAs(blob, `${filename}_${currentDateTime()}.csv`)
    }

    const downloadXLS = (items, columns, filename) => {
        const worksheet = XLSX.utils.json_to_sheet(formatData(items, columns))
        const workbook = XLSX.utils.book_new()

        XLSX.utils.book_append_sheet(workbook, worksheet, 'Export')

        const xlsOutput = XLSX.write(workbook, {
            bookType: 'xlsx',
            type: 'array',
        })

        const blob = new Blob([xlsOutput], {
            type: 'application/octet-stream',
        })

        saveAs(blob, `${filename}_${currentDateTime()}.xlsx`)
    }

    const createDOCX = (items, columns, title) => {
        const formattedData = formatData(items, columns)

        const rows = [
            new TableRow({
                children: columns.map((column) => new TableCell({
                    children: [
                        new Paragraph({
                            children: [new TextRun(column)],
                        }),
                    ],
                })),
            }),
        ]

        formattedData.forEach((item) => {
            rows.push(
                new TableRow({
                    children: columns.map((column) => new TableCell({
                        children: [
                            new Paragraph({
                                children: [new TextRun(String(item[column] ?? ''))],
                            }),
                        ],
                    })),
                })
            )
        })

        return new Document({
            sections: [
                {
                    properties: {
                        page: {
                            size: {
                                orientation: PageOrientation.LANDSCAPE,
                            },
                        },
                    },
                    children: [
                        new Paragraph({
                            text: title,
                            heading: 'Heading1',
                        }),
                        new Table({
                            rows,
                            width: {
                                size: 100,
                                type: WidthType.PERCENTAGE,
                            },
                        }),
                    ],
                },
            ],
        })
    }

    const downloadDOCX = async (items, columns, filename, title) => {
        const doc = createDOCX(items, columns, title)
        const blob = await Packer.toBlob(doc)

        saveAs(blob, `${filename}_${currentDateTime()}.docx`)
    }

    const downloadPDF = (items, columns, filename, title) => {
        const formattedData = formatData(items, columns)

        const headers = columns
            .map((column) => `<th>${column}</th>`)
            .join('')

        const rows = formattedData
            .map((item) => {
                const row = columns
                    .map((column) => `<td>${formatValue(item[column])}</td>`)
                    .join('')

                return `<tr>${row}</tr>`
            })
            .join('')

        const wrapper = document.createElement('div')

        wrapper.innerHTML = `
            <div style="font-family: Arial, sans-serif; font-size: 9px; color: #111; background: #fff;">
                <h1 style="font-size: 18px; margin-bottom: 12px;">${title}</h1>

                <style>
                    table {
                        width: 100%;
                        border-collapse: collapse;
                        table-layout: fixed;
                    }

                    th, td {
                        border: 1px solid #999;
                        padding: 4px 6px;
                        vertical-align: top;
                        word-break: break-word;
                        overflow-wrap: anywhere;
                    }

                    th {
                        background: #f0f0f0;
                        font-weight: bold;
                    }

                    tr {
                        page-break-inside: avoid;
                    }
                </style>

                <table>
                    <thead>
                        <tr>${headers}</tr>
                    </thead>
                    <tbody>
                        ${rows}
                    </tbody>
                </table>
            </div>
        `

        html2pdf()
            .set({
                margin: 0.35,
                filename: `${filename}_${currentDateTime()}.pdf`,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: {
                    scale: 1,
                    logging: false,
                    useCORS: true,
                    backgroundColor: '#ffffff',
                },
                jsPDF: {
                    unit: 'in',
                    format: 'a4',
                    orientation: 'landscape',
                },
                pagebreak: {
                    mode: ['css', 'legacy'],
                    avoid: ['tr'],
                },
            })
            .from(wrapper)
            .save()
    }

    const downloadZIP = async (items, columns, filename, title) => {
        const zip = new JSZip()
        const timestamp = currentDateTime()
        const formattedData = formatData(items, columns)

        const worksheet = XLSX.utils.json_to_sheet(formattedData)

        const csvOutput = XLSX.utils.sheet_to_csv(worksheet)
        zip.file(`${filename}_${timestamp}.csv`, '\uFEFF' + csvOutput)

        const workbook = XLSX.utils.book_new()
        XLSX.utils.book_append_sheet(workbook, worksheet, 'Export')

        const xlsOutput = XLSX.write(workbook, {
            bookType: 'xlsx',
            type: 'array',
        })

        zip.file(`${filename}_${timestamp}.xlsx`, xlsOutput)

        const doc = createDOCX(items, columns, title)
        const docBlob = await Packer.toBlob(doc)

        zip.file(`${filename}_${timestamp}.docx`, docBlob)

        const content = await zip.generateAsync({ type: 'blob' })

        saveAs(content, `${filename}_${timestamp}.zip`)
    }

    const download = async (format, items, columns, filename, title) => {
        if (!items?.length || !columns?.length) {
            alert('Нет данных для экспорта.')
            return
        }

        switch (format) {
            case 'csv':
                downloadCSV(items, columns, filename)
                break

            case 'xls':
                downloadXLS(items, columns, filename)
                break

            case 'docx':
                await downloadDOCX(items, columns, filename, title)
                break

            case 'pdf':
                downloadPDF(items, columns, filename, title)
                break

            case 'zip':
                await downloadZIP(items, columns, filename, title)
                break
        }
    }

    return {
        download,
    }
}

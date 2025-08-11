USE [bmn]
GO

SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO


ALTER PROCEDURE [dbo].[USP_TampilDataFinalPakingList]
@status VARCHAR(1),
@tahun  INT,
@userid VARCHAR(100)
AS
BEGIN
    -- Hapus temporary table jika sudah ada
    IF EXISTS (
        SELECT [Table_name]
        FROM tempdb.information_schema.tables
        WHERE [Table_name] LIKE '#temptess'
    )
    BEGIN
        DROP TABLE #temptess;
    END;

    -- Buat temporary table
    CREATE TABLE #temptess (
        No_Pls VARCHAR(20),
        No_Pli CHAR(20),
        NoPo CHAR(30),
        POTransacid CHAR(30),
        EntryDate DATETIME,
        Note VARCHAR(8000),
        supid CHAR(30),
        userid CHAR(30),
        Totaldetail FLOAT,
        Pib FLOAT,
        Forwarder FLOAT,
        Total FLOAT,
        UserPosting CHAR(10),
        DatePosting DATETIME,
        id_bl_awb CHAR(50),
		Note2 VARCHAR(8000),
        NamaProduk VARCHAR(30)
    );

    -- Jika status = 'Y', ambil semua data tahun itu
    IF(@status = 'Y')
    BEGIN
        INSERT INTO #temptess
        SELECT 
            p.No_Pls, p.No_Pli, p.NoPo, p.POTransacid, p.EntryDate, p.Note, p.supid,
            p.LastUserIDAccess,
            [bmn].[dbo].FunSumDetailPakingList_KURS_Temporary(p.No_Pls) AS Totaldetail,
            p.Pib, p.Forwarder, p.Total, p.UserPosting, p.DatePosting, p.id_bl_awb,p.Note2,p.NamaProduk
        FROM [bmn].[dbo].POPAKINGLIST_KURS_Temporary p
        WHERE 
            YEAR(p.EntryDate) = @tahun 
            AND p.FlagPosting = 'N'
           
        ORDER BY p.EntryDate DESC;
    END
    ELSE
    BEGIN
        -- Jika status selain 'Y', filter berdasarkan user ID
        INSERT INTO #temptess
        SELECT 
            p.No_Pls, p.No_Pli, p.NoPo, p.POTransacid, p.EntryDate, p.Note, p.supid,
            p.LastUserIDAccess,
            [bmn].[dbo].FunSumDetailPakingList_KURS_Temporary(p.No_Pls) AS Totaldetail,
            p.Pib, p.Forwarder, p.Total, p.UserPosting, p.DatePosting, p.id_bl_awb,p.Note2,p.NamaProduk
        FROM [bmn].[dbo].POPAKINGLIST_KURS_Temporary p
        WHERE 
            YEAR(p.EntryDate) = @tahun 
            AND p.LastUserIDAccess = @userid 
            AND p.FlagPosting = 'N'
        
        
        ORDER BY p.EntryDate DESC;
    END

    -- Ambil hasil akhir
    SELECT 
        No_Pls, No_Pli, NoPo, POTransacid, EntryDate, Note, supid, userid,
        Totaldetail, Pib, Forwarder, Total, UserPosting, DatePosting, id_bl_awb,Note2,NamaProduk
    FROM #temptess
    ORDER BY No_Pls ASC;
END
GO
EXEC USP_TampilDataFinalPakingList 'Y','2025','wardi' 
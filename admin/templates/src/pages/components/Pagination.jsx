import React from "react";

export default function Pagination({ page, totalPage, setPage, scrollTop }) {
  const getPages = () => {
    const pages = [];

    if (totalPage <= 7) {
      for (let i = 1; i <= totalPage; i++) pages.push(i);
    } else {
      if (page <= 4) {
        pages.push(1, 2, 3, 4, 5, "...", totalPage);
      } else if (page >= totalPage - 3) {
        pages.push(
          1,
          "...",
          totalPage - 4,
          totalPage - 3,
          totalPage - 2,
          totalPage - 1,
          totalPage
        );
      } else {
        pages.push(1, "...", page - 1, page, page + 1, "...", totalPage);
      }
    }

    return pages;
  };

  if (totalPage <= 1) return null;

  return (
    <div className="pagination">
      <button
        disabled={page === 1}
        onClick={() => {
          setPage(page - 1);
          scrollTop();
        }}
      >
        Prev
      </button>

      {getPages().map((p, i) =>
        p === "..." ? (
          <span key={i} className="dots">
            ...
          </span>
        ) : (
          <button
            key={i}
            className={page === p ? "active" : ""}
            onClick={() => {
              if (p === page) return;
              setPage(p);
              scrollTop();
            }}
          >
            {p}
          </button>
        )
      )}

      <button
        disabled={page === totalPage}
        onClick={() => {
          setPage(page + 1);
          scrollTop();
        }}
      >
        Next
      </button>
    </div>
  );
}

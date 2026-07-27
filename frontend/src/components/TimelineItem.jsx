const statusColors = {
  Registered: { dot: 'bg-blue-500 ring-blue-200', line: 'bg-blue-200' },
  'In Transit': { dot: 'bg-orange-500 ring-orange-200', line: 'bg-orange-200' },
  'Out for Delivery': { dot: 'bg-amber-500 ring-amber-200', line: 'bg-amber-200' },
  Delivered: { dot: 'bg-green-500 ring-green-200', line: 'bg-green-200' },
  Cancelled: { dot: 'bg-red-500 ring-red-200', line: 'bg-red-200' },
};

export default function TimelineItem({ item, isLast }) {
  const colors = statusColors[item.new_status] || { dot: 'bg-gray-400 ring-gray-200', line: 'bg-gray-200' };
  const time = new Date(item.created_at).toLocaleString('en-US', {
    month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit',
  });

  return (
    <div className="relative pb-6">
      {!isLast && (
        <div className={`absolute left-[0.625rem] top-4 w-0.5 h-full ${colors.line}`} />
      )}
      <div className="flex gap-4 items-start">
        <div className={`w-5 h-5 rounded-full ${colors.dot} ring-4 ${colors.dot.replace('bg-', 'ring-').replace('500', '100')} flex-shrink-0 z-10`} />
        <div className="flex-1 min-w-0">
          <div className="flex items-center gap-2 flex-wrap">
            <span className={`text-xs font-semibold px-2 py-0.5 rounded-full bg-${colors.dot.split('-')[1]}-100 text-${colors.dot.split('-')[1]}-700`}>
              {item.new_status}
            </span>
            <span className="text-xs text-gray-400">{time}</span>
          </div>
          {item.comment && <p className="text-sm text-gray-600 mt-1">{item.comment}</p>}
          {item.location && (
            <p className="text-xs text-gray-400 mt-0.5 flex items-center gap-1">
              <svg className="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2}
                  d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2}
                  d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              {item.location}
            </p>
          )}
        </div>
      </div>
    </div>
  );
}

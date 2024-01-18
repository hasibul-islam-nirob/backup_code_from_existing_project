<?php


## mfn_loans টেবিল থেকে কোনপ্রকার ডাটা এডিট বা ডিলিট করা যাবেনা। যদি first repay date পরিবর্তন করা হয় তাহলে last installment date ও পরিবর্তন হবে। 
## এবং এই first repay date দিয়ে mfn_loan_schedule এ ডাটা এন্ট্রি পরে সুতরাং first repay date যদি database থেকে পরিবর্তন করা হয় তাহলে ডাটাতে অনেক গড়মিল হতে পারে। 
## সুতরাং database থেকে কোনপ্রকার ডাটা পরিবর্তন না করার অনুরোধ রইল।

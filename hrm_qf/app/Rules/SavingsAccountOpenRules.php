<?php

## Loan Disbursement এর ক্ষেত্রে Savings Account Open করার সময় আমাদের কিছুক্ষেত্রে নজর রাখতে হবে।
## Loan Disbursement করার সময় আমরা GS এবং MVS এই ২টা Savings Account Open অথবা Update করা হয়।
## যদি আমাদের প্রোডাক্ট এর একাধিক সাভিংস গ্রহণযোগ্য হয় তাহলে কোন চেক না করেই নতুন অ্যাকাউন্ট তৈরি করবে।
## আর যদি প্রোডাক্ট এর একাধিক সাভিংস গ্রহণযোগ্য না হয় তাহলে আগের অ্যাক্টিভ সাভিংস অ্যাকাউন্ট এর AutoProcessAmount 
## আপডেট হয়ে যাবে। কিন্তু যদি আগের অ্যাক্টিভ সাভিংস না থাকে তাহলে নতুন সাভিংস অ্যাকাউন্ট তৈরি করবে।
<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddDrugRequest;
use App\Http\Resources\DrugResource;
use App\Http\Resources\PrescriptionResource;
use App\Pharmacy;
use App\Prescription;
use Illuminate\Http\Request;
use App\DrugsPrescribed;
class PharmacyController extends Controller
{
    //

    public function __construct()
    {
        # code...
        $this->middleware(['multiauth:pharmacist','api'])->except(['index']);
    }

    public function index() 
    {
        return DrugResource::collection(Pharmacy::all());
    }
    public function addDrug(AddDrugRequest $request) 
    {
            $drug = Pharmacy::create($request->all());

            return response()->json([
                'drug' => $drug
            ]);
    }

    public function updateDrug(Pharmacy $pharmacy)
    {
        $drug = $pharmacy->update([
            'drug_name' => request()->drug_name,
            'quantity' => request()->quantity
        ]);

        return response()->json([
            'drug' => $pharmacy
        ]);
    }

    public function prescriptions()
    {
        $prescriptions =  Prescription::where([
            'submitted' => true,
            'drug_issued' => false
        ])->get();

        return PrescriptionResource::collection($prescriptions);
    }

    public function prescription(Prescription $prescription)
    {
        return new PrescriptionResource($prescription);
    }

    public function issueDrugs(Prescription $prescription)
    {
        $data = request()->all()['drugs'];

        for ($i=0; $i < count($data); $i++) { 
            DrugsPrescribed::create([
                'prescription_id' => $prescription->id,
                'pharmacy_id' => $data[$i]['pharmacy_id'],
                'quantity' => $data[$i]['quantity']
            ]);
        }
        $prescription->drug_issued = true;
        $prescription->save();
        
        return response()->json(
            $data
        );
        
    }
    
}
